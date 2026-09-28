<?php

namespace App\Services;

use App\Models\PortfolioItem;
use App\Support\ExifOrientation;
use App\Support\IccProfile;
use GdImage;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Prepares portfolio photos for the public site:
 *
 *  - caps the stored master at MAX_EDGE px on its longest side, writing the
 *    smaller copy under a NEW key and leaving the original object untouched,
 *    so nothing is lost and every URL ever served keeps its exact bytes
 *    (which is what makes the year-long "immutable" cache header safe);
 *  - records the master's width/height so the page can reserve its space;
 *  - writes resized WebP copies for the grid's srcset and the lightbox;
 *  - gives every one of those objects a long-lived Cache-Control header.
 */
class PortfolioImageProcessor
{
    public const MAX_EDGE = 2500;

    /** Grid srcset candidates, in px wide. Never upscaled past the master. */
    public const GRID_WIDTHS = [480, 800, 1200];

    /** Longest side of the lightbox copy. */
    public const LIGHTBOX_EDGE = 1800;

    public const CACHE_CONTROL = 'public, max-age=31536000, immutable';

    public const VARIANT_DIRECTORY = 'portfolio-variants';

    private const WEBP_QUALITY = 80;

    /** Formats GD can re-encode as themselves, with the extension to store them under. */
    private const FORMATS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(private ?FilesystemAdapter $disk = null) {}

    /** Whether the path points at the r2 disk (older items may hold a full external URL). */
    public static function isStoredOnDisk(?string $path): bool
    {
        return filled($path) && ! Str::startsWith($path, ['http://', 'https://']);
    }

    public static function variantPath(string $imagePath, int $width): string
    {
        return self::VARIANT_DIRECTORY.'/'.pathinfo($imagePath, PATHINFO_FILENAME)."-{$width}w.webp";
    }

    /**
     * Widths to generate for a master of the given size: the grid widths
     * (capped at the master's width) plus the lightbox copy, with
     * near-duplicates (within 10%) collapsed into the larger one.
     *
     * @return list<int>
     */
    public static function variantWidths(int $width, int $height): array
    {
        $candidates = array_map(fn (int $w) => min($w, $width), self::GRID_WIDTHS);
        $candidates[] = self::lightboxWidth($width, $height);
        $candidates = array_values(array_unique($candidates));
        sort($candidates);

        $widths = [];

        foreach ($candidates as $candidate) {
            if ($widths !== [] && $candidate <= end($widths) * 1.1) {
                array_pop($widths);
            }

            $widths[] = $candidate;
        }

        return $widths;
    }

    /** Width of the copy whose longest side is LIGHTBOX_EDGE, or the master's own width if it is smaller. */
    public static function lightboxWidth(int $width, int $height): int
    {
        $longest = max($width, $height);

        return $longest > self::LIGHTBOX_EDGE
            ? (int) round($width * self::LIGHTBOX_EDGE / $longest)
            : $width;
    }

    /**
     * Everything a newly uploaded image needs: cap its size, then build the variants.
     *
     * @return array<string, mixed>
     */
    public function process(PortfolioItem $item): array
    {
        $downscale = $this->downscale($item);

        if ($downscale['status'] === 'error') {
            return $downscale;
        }

        return ['downscale' => $downscale] + $this->generateVariants($item, force: $downscale['status'] === 'optimized');
    }

    /**
     * Replaces an over-sized master with a copy capped at $maxEdge on its
     * longest side. The copy goes to a new key and the item is pointed at it;
     * the original object is left in place as the backup.
     *
     * @return array<string, mixed>
     */
    public function downscale(PortfolioItem $item, int $maxEdge = self::MAX_EDGE, int $quality = 85, bool $dryRun = false): array
    {
        $path = $item->image_path;

        if (! self::isStoredOnDisk($path)) {
            return ['status' => 'skipped', 'reason' => 'external URL, not on the r2 disk'];
        }

        try {
            $master = $this->load($path);
        } catch (RuntimeException $e) {
            return ['status' => 'error', 'reason' => $e->getMessage()];
        }

        $width = imagesx($master['image']);
        $height = imagesy($master['image']);
        $report = ['path' => $path, 'from' => [$width, $height], 'bytes_before' => $master['bytes']];

        if (max($width, $height) <= $maxEdge) {
            imagedestroy($master['image']);

            return $report + ['status' => 'skipped', 'reason' => "already within {$maxEdge}px"];
        }

        if (! isset(self::FORMATS[$master['mime']])) {
            imagedestroy($master['image']);

            return $report + ['status' => 'skipped', 'reason' => "unsupported format {$master['mime']}"];
        }

        $scale = $maxEdge / max($width, $height);
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));
        $resized = $this->resize($master['image'], $newWidth, $newHeight);
        imagedestroy($master['image']);

        $encoded = $this->encode($resized, $master['mime'], $quality, $master['icc']);
        imagedestroy($resized);

        $newPath = 'portfolio-images/'.Str::uuid().'.'.self::FORMATS[$master['mime']];
        $report += ['to' => [$newWidth, $newHeight], 'bytes_after' => strlen($encoded), 'new_path' => $newPath];

        if ($dryRun) {
            return $report + ['status' => 'would-optimize'];
        }

        $this->put($newPath, $encoded, $master['mime']);

        // Quietly: the model's own "image changed" hook would otherwise queue
        // a second, redundant processing run for the key we just wrote.
        $item->forceFill([
            'image_path' => $newPath,
            'width' => $newWidth,
            'height' => $newHeight,
            'variant_widths' => null,
        ])->saveQuietly();

        return $report + ['status' => 'optimized'];
    }

    /**
     * Records the master's dimensions, writes its WebP variants and sets the
     * long-lived cache header on the master.
     *
     * @return array<string, mixed>
     */
    public function generateVariants(PortfolioItem $item, bool $force = false): array
    {
        $path = $item->image_path;

        if (! self::isStoredOnDisk($path)) {
            return ['status' => 'skipped', 'reason' => 'external URL, not on the r2 disk'];
        }

        if (! $force && $item->width && $item->height && filled($item->variant_widths)) {
            return ['status' => 'skipped', 'reason' => 'variants already exist'];
        }

        if (! (gd_info()['WebP Support'] ?? false)) {
            return ['status' => 'error', 'reason' => 'this PHP build of GD cannot write WebP'];
        }

        try {
            $master = $this->load($path);
        } catch (RuntimeException $e) {
            return ['status' => 'error', 'reason' => $e->getMessage()];
        }

        $width = imagesx($master['image']);
        $height = imagesy($master['image']);
        $widths = self::variantWidths($width, $height);
        $bytes = [];

        foreach ($widths as $variantWidth) {
            $variantHeight = max(1, (int) round($height * $variantWidth / $width));
            $resized = $variantWidth === $width
                ? $master['image']
                : $this->resize($master['image'], $variantWidth, $variantHeight);

            $encoded = $this->encode($resized, 'image/webp', self::WEBP_QUALITY, $master['icc']);

            if ($resized !== $master['image']) {
                imagedestroy($resized);
            }

            $this->put(self::variantPath($path, $variantWidth), $encoded, 'image/webp');
            $bytes[$variantWidth] = strlen($encoded);
        }

        imagedestroy($master['image']);

        $cacheHeaderSet = $this->applyCacheControl($path, $master['mime']);

        $item->forceFill([
            'width' => $width,
            'height' => $height,
            'variant_widths' => $widths,
        ])->saveQuietly();

        return [
            'status' => 'generated',
            'path' => $path,
            'size' => [$width, $height],
            'master_bytes' => $master['bytes'],
            'variant_bytes' => $bytes,
            'cache_header' => $cacheHeaderSet,
        ];
    }

    /**
     * Rewrites an existing object's metadata in place (an S3 copy onto
     * itself) so it carries the long-lived cache header. The bytes do not
     * change. Only possible on a real S3-compatible disk; a no-op on the
     * local/fake disks used in development and tests.
     */
    public function applyCacheControl(string $path, string $mime): bool
    {
        $disk = $this->disk();

        if (! $disk instanceof AwsS3V3Adapter) {
            return false;
        }

        $bucket = $disk->getConfig()['bucket'];
        $key = $disk->path($path);
        $client = $disk->getClient();

        $head = $client->headObject(['Bucket' => $bucket, 'Key' => $key]);

        if (($head['CacheControl'] ?? null) === self::CACHE_CONTROL) {
            return true;
        }

        $client->copyObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'CopySource' => $bucket.'/'.implode('/', array_map('rawurlencode', explode('/', $key))),
            'MetadataDirective' => 'REPLACE',
            'CacheControl' => self::CACHE_CONTROL,
            'ContentType' => $head['ContentType'] ?? $mime,
        ]);

        return true;
    }

    private function disk(): FilesystemAdapter
    {
        return $this->disk ??= Storage::disk('r2');
    }

    private function put(string $path, string $contents, string $mime): void
    {
        $written = $this->disk()->put($path, $contents, [
            'visibility' => 'public',
            'CacheControl' => self::CACHE_CONTROL,
            'ContentType' => $mime,
        ]);

        if ($written === false) {
            throw new RuntimeException("Could not write {$path} to the r2 disk");
        }
    }

    /**
     * @return array{image: GdImage, mime: string, bytes: int, icc: ?string}
     */
    private function load(string $path): array
    {
        $disk = $this->disk();

        if (! $disk->exists($path)) {
            throw new RuntimeException("{$path} not found on the r2 disk");
        }

        $contents = $disk->get($path);

        if (! is_string($contents) || $contents === '') {
            throw new RuntimeException("{$path} could not be read");
        }

        $info = @getimagesizefromstring($contents);

        if ($info === false) {
            throw new RuntimeException("{$path} is not a readable image");
        }

        // A 5000x7500 photo decodes to ~190MB of pixels, well past PHP's
        // default 128MB limit, so give decoding room in proportion to the image.
        $this->ensureMemory((int) ($info[0] * $info[1] * 5 * 2.5));

        $image = @imagecreatefromstring($contents);

        if ($image === false) {
            throw new RuntimeException("{$path} could not be decoded as an image");
        }

        $icc = null;

        if ($info['mime'] === 'image/jpeg') {
            $image = $this->orient($image, ExifOrientation::fromJpeg($contents));
            $icc = IccProfile::fromJpeg($contents);
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        return [
            'image' => $image,
            'mime' => $info['mime'],
            'bytes' => strlen($contents),
            // Wide-gamut profiles (Adobe RGB, Display P3) must travel with the copies.
            'icc' => IccProfile::isWorthKeeping($icc) ? $icc : null,
        ];
    }

    private function orient(GdImage $image, int $orientation): GdImage
    {
        $rotate = match ($orientation) {
            3 => 180,
            5, 6 => -90,
            7, 8 => 90,
            default => 0,
        };

        if ($rotate !== 0) {
            $rotated = imagerotate($image, $rotate, 0);
            imagedestroy($image);
            $image = $rotated;
        }

        if (in_array($orientation, [2, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        } elseif ($orientation === 4) {
            imageflip($image, IMG_FLIP_VERTICAL);
        }

        return $image;
    }

    private function resize(GdImage $source, int $width, int $height): GdImage
    {
        $resized = imagecreatetruecolor($width, $height);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        return $resized;
    }

    private function encode(GdImage $image, string $mime, int $quality, ?string $icc = null): string
    {
        ob_start();

        match ($mime) {
            'image/png' => imagepng($image, null, 6),
            'image/webp' => imagewebp($image, null, $quality),
            default => imagejpeg($image, null, $quality),
        };

        $encoded = (string) ob_get_clean();

        if ($icc === null) {
            return $encoded;
        }

        return match ($mime) {
            'image/webp' => IccProfile::embedInWebp($encoded, $icc, imagesx($image), imagesy($image)),
            'image/jpeg' => IccProfile::embedInJpeg($encoded, $icc),
            default => $encoded, // PNG masters: no profile carried over
        };
    }

    private function ensureMemory(int $bytes): void
    {
        $limit = trim((string) ini_get('memory_limit'));

        if ($limit === '-1') {
            return;
        }

        $current = (int) $limit * match (strtolower(substr($limit, -1))) {
            'g' => 1024 ** 3,
            'm' => 1024 ** 2,
            'k' => 1024,
            default => 1,
        };

        $wanted = memory_get_usage() + $bytes;

        if ($wanted > $current) {
            ini_set('memory_limit', (int) ceil($wanted / 1024 / 1024).'M');
        }
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\ProcessPortfolioImage;
use App\Models\Category;
use App\Models\PortfolioItem;
use App\Services\PortfolioImageProcessor;
use App\Support\ExifOrientation;
use App\Support\IccProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('gd')]
class PortfolioImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('r2');
        $this->category = Category::create(['name' => 'Weddings', 'slug' => 'weddings']);
    }

    private function jpeg(int $width, int $height, int $orientation = 1): string
    {
        $image = imagecreatetruecolor($width, $height);
        // Left half red, right half blue: lets a test tell which way the photo was turned.
        imagefilledrectangle($image, 0, 0, intdiv($width, 2), $height, imagecolorallocate($image, 255, 0, 0));
        imagefilledrectangle($image, intdiv($width, 2) + 1, 0, $width, $height, imagecolorallocate($image, 0, 0, 255));
        ob_start();
        imagejpeg($image, null, 90);
        $bytes = ob_get_clean();

        if ($orientation === 1) {
            return $bytes;
        }

        // Minimal little-endian EXIF block holding just the orientation tag.
        $tiff = 'II'.pack('v', 42).pack('V', 8)
            .pack('v', 1).pack('vvVvv', 0x0112, 3, 1, $orientation, 0)
            .pack('V', 0);
        $payload = "Exif\0\0".$tiff;

        return "\xFF\xD8\xFF\xE1".pack('n', strlen($payload) + 2).$payload.substr($bytes, 2);
    }

    private function png(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        imagepng($image);

        return ob_get_clean();
    }

    private function upload(string $path, string $bytes): PortfolioItem
    {
        Storage::disk('r2')->put($path, $bytes);

        return PortfolioItem::create(['category_id' => $this->category->id, 'image_path' => $path]);
    }

    private function dimensionsOf(string $path): array
    {
        $info = getimagesizefromstring(Storage::disk('r2')->get($path));

        return [$info[0], $info[1], $info['mime']];
    }

    public function test_variant_widths_follow_the_master_size(): void
    {
        $this->assertSame([480, 800, 1200, 1800], PortfolioImageProcessor::variantWidths(2500, 1667));
        // Portrait: the lightbox copy (1200x1800) coincides with the largest grid width.
        $this->assertSame([480, 800, 1200], PortfolioImageProcessor::variantWidths(1667, 2500));
        // Never upscaled; a near-duplicate 480 collapses into the 485 master width.
        $this->assertSame([485], PortfolioImageProcessor::variantWidths(485, 679));
    }

    public function test_exif_orientation_is_read_in_both_byte_orders(): void
    {
        $this->assertSame(6, ExifOrientation::fromJpeg($this->jpeg(40, 20, 6)));
        $this->assertSame(1, ExifOrientation::fromJpeg($this->jpeg(40, 20)));
        $this->assertSame(1, ExifOrientation::fromJpeg('not a jpeg'));

        $tiff = 'MM'.pack('n', 42).pack('N', 8).pack('n', 1).pack('nnNnn', 0x0112, 3, 1, 8, 0).pack('N', 0);
        $payload = "Exif\0\0".$tiff;
        $bigEndian = "\xFF\xD8\xFF\xE1".pack('n', strlen($payload) + 2).$payload."\xFF\xDA";

        $this->assertSame(8, ExifOrientation::fromJpeg($bigEndian));
    }

    /** A minimal ICC profile: 128-byte header, one tag (desc) with an ASCII name. */
    private function iccProfile(string $name): string
    {
        $desc = 'desc'."\0\0\0\0".pack('N', strlen($name) + 1).$name."\0";

        return str_repeat("\0", 128).pack('N', 1).'desc'.pack('NN', 144, strlen($desc)).$desc;
    }

    private function withIcc(string $jpeg, string ...$profiles): string
    {
        // Embedding in reverse order leaves the first profile first in the file.
        foreach (array_reverse($profiles) as $profile) {
            $jpeg = IccProfile::embedInJpeg($jpeg, $profile);
        }

        return $jpeg;
    }

    public function test_icc_profiles_are_read_like_chrome_reads_them(): void
    {
        $adobe = $this->iccProfile('Adobe RGB (1998)');
        $srgb = $this->iccProfile('sRGB IEC61966-2.1');

        $this->assertSame($adobe, IccProfile::fromJpeg($this->withIcc($this->jpeg(20, 20), $adobe)));
        // Two "1 of 1" profiles: Chrome uses the first, so do we.
        $this->assertSame($adobe, IccProfile::fromJpeg($this->withIcc($this->jpeg(20, 20), $adobe, $srgb)));
        $this->assertNull(IccProfile::fromJpeg($this->jpeg(20, 20)));

        // Profiles over 64KB span several numbered segments.
        $large = $this->iccProfile('Big').str_repeat('x', 150000);
        $this->assertSame($large, IccProfile::fromJpeg($this->withIcc($this->jpeg(20, 20), $large)));

        $this->assertTrue(IccProfile::isWorthKeeping($adobe));
        $this->assertFalse(IccProfile::isWorthKeeping($srgb));
        $this->assertSame('Adobe RGB (1998)', IccProfile::description($adobe));
    }

    public function test_wide_gamut_profile_travels_with_variants_and_downscaled_masters(): void
    {
        $adobe = $this->iccProfile('Adobe RGB (1998)');
        $item = $this->upload('portfolio-images/adobe.jpg', $this->withIcc($this->jpeg(3000, 2000), $adobe))->refresh();

        $this->assertSame($adobe, IccProfile::fromJpeg(Storage::disk('r2')->get($item->image_path)));

        $webp = Storage::disk('r2')->get(PortfolioImageProcessor::variantPath($item->image_path, 800));
        $this->assertSame('VP8X', substr($webp, 12, 4));
        $this->assertSame(0x20, ord($webp[20]) & 0x20, 'ICC flag set');
        $this->assertSame('ICCP', substr($webp, 30, 4));
        $this->assertSame($adobe, substr($webp, 38, strlen($adobe)));
        $this->assertSame(strlen($webp) - 8, unpack('V', substr($webp, 4, 4))[1], 'RIFF size');
        $this->assertSame([800, 533], array_slice(getimagesizefromstring($webp), 0, 2));
        $this->assertNotFalse(imagecreatefromstring($webp));
    }

    public function test_srgb_profiles_are_not_copied(): void
    {
        $item = $this->upload('portfolio-images/srgb.jpg', $this->withIcc($this->jpeg(900, 600), $this->iccProfile('sRGB')))->refresh();

        $webp = Storage::disk('r2')->get(PortfolioImageProcessor::variantPath($item->image_path, 480));
        $this->assertStringNotContainsString('ICCP', substr($webp, 0, 64));
    }

    public function test_upload_is_downscaled_to_a_new_key_and_the_original_is_kept(): void
    {
        $item = $this->upload('portfolio-images/original.jpg', $this->jpeg(3000, 2000))->refresh();

        $this->assertNotSame('portfolio-images/original.jpg', $item->image_path);
        $this->assertStringEndsWith('.jpg', $item->image_path);
        Storage::disk('r2')->assertExists('portfolio-images/original.jpg');
        $this->assertSame([2500, 1667, 'image/jpeg'], $this->dimensionsOf($item->image_path));

        $this->assertSame(2500, $item->width);
        $this->assertSame(1667, $item->height);
        $this->assertSame([480, 800, 1200, 1800], $item->variant_widths);

        foreach ([480, 800, 1200, 1800] as $width) {
            $path = PortfolioImageProcessor::variantPath($item->image_path, $width);
            [$w, , $mime] = $this->dimensionsOf($path);
            $this->assertSame($width, $w, $path);
            $this->assertSame('image/webp', $mime, $path);
        }
    }

    public function test_portrait_is_capped_on_its_longest_edge_not_its_width(): void
    {
        // Only 2000px wide, so the old width-only check would have let it through.
        $item = $this->upload('portfolio-images/tall.jpg', $this->jpeg(2000, 3000))->refresh();

        $this->assertSame([1667, 2500, 'image/jpeg'], $this->dimensionsOf($item->image_path));
        $this->assertSame([480, 800, 1200], $item->variant_widths);
    }

    public function test_png_stays_png(): void
    {
        $item = $this->upload('portfolio-images/graphic.png', $this->png(2600, 1000))->refresh();

        $this->assertStringEndsWith('.png', $item->image_path);
        $this->assertSame([2500, 962, 'image/png'], $this->dimensionsOf($item->image_path));
    }

    public function test_exif_rotation_is_applied_to_dimensions_and_variants(): void
    {
        // Stored landscape, tagged "rotate 90° clockwise": it is really a portrait.
        $item = $this->upload('portfolio-images/phone.jpg', $this->jpeg(1000, 600, 6))->refresh();

        $this->assertSame('portfolio-images/phone.jpg', $item->image_path, 'within the size cap, so the master is untouched');
        $this->assertSame([600, 1000], [$item->width, $item->height]);

        [$w, $h] = $this->dimensionsOf(PortfolioImageProcessor::variantPath($item->image_path, 480));
        $this->assertSame([480, 800], [$w, $h]);
    }

    public function test_small_images_keep_their_master_and_get_variants(): void
    {
        $item = $this->upload('portfolio-images/small.jpg', $this->jpeg(1200, 800))->refresh();

        $this->assertSame('portfolio-images/small.jpg', $item->image_path);
        $this->assertSame([480, 800, 1200], $item->variant_widths);
    }

    public function test_external_urls_are_not_processed(): void
    {
        Queue::fake();

        PortfolioItem::create(['category_id' => $this->category->id, 'image_path' => 'https://images.example.test/a.jpg']);

        Queue::assertNothingPushed();
    }

    public function test_replacing_the_image_clears_stale_variants_and_requeues(): void
    {
        $item = $this->upload('portfolio-images/first.jpg', $this->jpeg(900, 600))->refresh();
        $this->assertNotNull($item->variant_widths);

        Queue::fake();
        Storage::disk('r2')->put('portfolio-images/second.jpg', $this->jpeg(900, 600));
        $item->update(['image_path' => 'portfolio-images/second.jpg']);

        $item->refresh();
        $this->assertNull($item->width);
        $this->assertNull($item->variant_widths);
        Queue::assertPushed(ProcessPortfolioImage::class, fn ($job) => $job->portfolioItemId === $item->id);
    }

    public function test_optimize_command_dry_run_writes_nothing(): void
    {
        Queue::fake(); // leave the upload unprocessed, as legacy items are
        $item = $this->upload('portfolio-images/legacy.jpg', $this->jpeg(3000, 2000));
        $filesBefore = Storage::disk('r2')->allFiles();

        $this->artisan('portfolio:optimize-images', ['--dry-run' => true])
            ->expectsOutputToContain('Would optimize 1, skipped 0, errors 0')
            ->assertSuccessful();

        $this->assertSame('portfolio-images/legacy.jpg', $item->refresh()->image_path);
        $this->assertSame($filesBefore, Storage::disk('r2')->allFiles());
    }

    public function test_optimize_command_downscales_reports_and_keeps_originals(): void
    {
        Queue::fake();
        $big = $this->upload('portfolio-images/legacy.jpg', $this->jpeg(3000, 2000));
        $fine = $this->upload('portfolio-images/fine.jpg', $this->jpeg(1000, 800));
        PortfolioItem::create(['category_id' => $this->category->id, 'image_path' => 'portfolio-images/missing.jpg']);

        $this->artisan('portfolio:optimize-images')
            ->expectsOutputToContain('Optimized 1, skipped 1, errors 1')
            ->expectsOutputToContain('nothing was deleted')
            ->assertFailed(); // the missing file counts as an error

        $big->refresh();
        $this->assertNotSame('portfolio-images/legacy.jpg', $big->image_path);
        $this->assertSame([480, 800, 1200, 1800], $big->variant_widths);
        Storage::disk('r2')->assertExists('portfolio-images/legacy.jpg');
        $this->assertSame('portfolio-images/fine.jpg', $fine->refresh()->image_path);
    }

    public function test_generate_variants_command_backfills_and_skips_done_items(): void
    {
        Queue::fake();
        $item = $this->upload('portfolio-images/legacy.jpg', $this->jpeg(1600, 1000));

        $this->artisan('portfolio:generate-variants')
            ->expectsOutputToContain('Generated 1, skipped 0, errors 0')
            ->assertSuccessful();

        $this->assertSame([1600, 1000], [$item->refresh()->width, $item->height]);

        $this->artisan('portfolio:generate-variants')
            ->expectsOutputToContain('Generated 0, skipped 1, errors 0')
            ->assertSuccessful();
    }

    public function test_portfolio_page_serves_responsive_images_with_dimensions(): void
    {
        $item = $this->upload('portfolio-images/shot.jpg', $this->jpeg(2500, 1667))->refresh();
        $html = $this->get(route('portfolio'))->assertOk()->getContent();

        $variant = fn (int $w) => Storage::disk('r2')->url(PortfolioImageProcessor::variantPath($item->image_path, $w));

        $this->assertStringContainsString('<source type="image/webp" srcset="'.$variant(480).' 480w, '.$variant(800).' 800w, '.$variant(1200).' 1200w, '.$variant(1800).' 1800w"', $html);
        $this->assertStringContainsString('sizes="(min-width: 1320px) calc((1320px - 12vw - 72px) / 3)', $html);
        $this->assertStringContainsString('width="2500" height="1667"', $html);
        $this->assertStringContainsString('loading="lazy" decoding="async"', $html);
        $this->assertStringContainsString('data-full="'.$variant(1800).'"', $html);

        // Keyboard-operable items and a communicated filter state.
        $this->assertMatchesRegularExpression('#<button type="button" class="gallery-item[^"]*"[^>]*aria-label="View photo: Weddings"#', $html);
        $this->assertStringContainsString('data-filter="all" aria-pressed="true"', $html);
        $this->assertStringContainsString('data-filter="weddings" aria-pressed="false"', $html);
        $this->assertStringContainsString('role="dialog" aria-modal="true"', $html);
    }

    public function test_unprocessed_items_fall_back_to_the_master(): void
    {
        Queue::fake();
        $item = $this->upload('portfolio-images/new.jpg', $this->jpeg(800, 600));

        $html = $this->get(route('portfolio'))->assertOk()->getContent();

        $this->assertStringNotContainsString('<source', $html);
        $this->assertStringContainsString('data-full="'.$item->imageUrl().'"', $html);
        $this->assertMatchesRegularExpression('#src="'.preg_quote($item->imageUrl(), '#').'" alt="Weddings"\s+loading="lazy"#', $html);
    }

    public function test_home_filmstrip_uses_variants_and_lazy_loading(): void
    {
        $item = $this->upload('portfolio-images/shot.jpg', $this->jpeg(2500, 1667))->refresh();
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('sizes="(min-width: 768px) 34vw, 78vw"', $html);
        $this->assertMatchesRegularExpression('#src="'.preg_quote($item->imageUrl(), '#').'" alt="Weddings" width="2500" height="1667"\s+loading="lazy"#', $html);
    }

    public function test_home_page_no_longer_loads_every_portfolio_item(): void
    {
        Queue::fake();
        foreach (range(1, 10) as $i) {
            PortfolioItem::create(['category_id' => $this->category->id, 'image_path' => "https://images.example.test/{$i}.jpg"]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get(route('home'))->assertOk();

        $portfolioQueries = collect(DB::getQueryLog())
            ->pluck('query')
            ->filter(fn (string $sql) => str_contains($sql, 'from "portfolio_items"'));

        // Just the filmstrip's random seven; no categories-with-all-items query.
        $this->assertCount(1, $portfolioQueries, $portfolioQueries->implode("\n"));
    }
}

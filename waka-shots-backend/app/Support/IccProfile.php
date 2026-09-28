<?php

namespace App\Support;

/**
 * Carries a photo's embedded colour profile over to the copies GD makes.
 *
 * GD drops ICC profiles when it re-encodes, and browsers then read the pixels
 * as sRGB. For an Adobe RGB or Display P3 original that visibly flattens the
 * colours, so the profile is copied out of the source JPEG and embedded into
 * each JPEG/WebP copy unchanged; the pixel values themselves are untouched.
 */
class IccProfile
{
    private const JPEG_MARKER = "ICC_PROFILE\0";

    /** Max profile bytes per APP2 segment: 65535 - 2 (length) - 12 (marker) - 2 (seq/count). */
    private const JPEG_CHUNK = 65519;

    public static function fromJpeg(string $bytes): ?string
    {
        if (strncmp($bytes, "\xFF\xD8", 2) !== 0) {
            return null;
        }

        $chunks = [];
        $length = strlen($bytes);
        $offset = 2;

        while ($offset + 4 <= $length && $bytes[$offset] === "\xFF") {
            $marker = ord($bytes[$offset + 1]);

            if ($marker === 0xFF) {
                $offset++;

                continue;
            }

            if ($marker === 0xDA || $marker === 0xD9) {
                break;
            }

            $size = unpack('n', substr($bytes, $offset + 2, 2))[1];

            if ($marker === 0xE2 && substr($bytes, $offset + 4, 12) === self::JPEG_MARKER) {
                $sequence = ord($bytes[$offset + 16]);
                $expected = ord($bytes[$offset + 17]);

                // Some exports carry two profiles both numbered "1 of 1" (Adobe
                // RGB, then sRGB). Chrome renders such a photo with the first
                // one, so keep the first and ignore later duplicates: the copies
                // then look exactly like the original does today.
                if (! isset($chunks[$sequence])) {
                    $chunks[$sequence] = substr($bytes, $offset + 18, $size - 16);
                }
            }

            $offset += 2 + $size;
        }

        if ($chunks === [] || count($chunks) !== ($expected ?? 0)) {
            return null;
        }

        ksort($chunks);

        return implode('', $chunks);
    }

    /**
     * Whether a profile needs carrying over at all. Untagged images are
     * already treated as sRGB, so copying an sRGB profile only adds ~3KB.
     */
    public static function isWorthKeeping(?string $profile): bool
    {
        if ($profile === null || strlen($profile) < 132) {
            return false;
        }

        $description = self::description($profile);

        return $description === null || ! str_contains($description, 'sRGB');
    }

    /** The profile's human-readable name ("Adobe RGB (1998)", "Display P3", ...). */
    public static function description(string $profile): ?string
    {
        $tags = unpack('N', substr($profile, 128, 4))[1];

        for ($i = 0; $i < $tags; $i++) {
            $entry = substr($profile, 132 + $i * 12, 12);

            if (strlen($entry) < 12) {
                return null;
            }

            ['sig' => $signature, 'offset' => $offset, 'size' => $size] = unpack('a4sig/Noffset/Nsize', $entry);

            if ($signature !== 'desc') {
                continue;
            }

            $data = substr($profile, $offset, $size);

            if (str_starts_with($data, 'desc') && strlen($data) >= 12) {
                $length = unpack('N', substr($data, 8, 4))[1];

                return rtrim(substr($data, 12, $length), "\0");
            }

            if (str_starts_with($data, 'mluc') && strlen($data) >= 28) {
                ['length' => $length, 'start' => $start] = unpack('Nlength/Nstart', substr($data, 20, 8));

                return mb_convert_encoding(substr($data, $start, $length), 'UTF-8', 'UTF-16BE');
            }

            return null;
        }

        return null;
    }

    /** Inserts the profile as APP2 segment(s) straight after the JFIF header (or SOI). */
    public static function embedInJpeg(string $jpeg, string $profile): string
    {
        $segments = str_split($profile, self::JPEG_CHUNK);
        $count = count($segments);

        if ($count > 255 || strncmp($jpeg, "\xFF\xD8", 2) !== 0) {
            return $jpeg;
        }

        $app2 = '';

        foreach ($segments as $i => $segment) {
            $payload = self::JPEG_MARKER.chr($i + 1).chr($count).$segment;
            $app2 .= "\xFF\xE2".pack('n', strlen($payload) + 2).$payload;
        }

        $insertAt = 2;

        if (substr($jpeg, 2, 2) === "\xFF\xE0") { // keep JFIF APP0 first
            $insertAt = 4 + unpack('n', substr($jpeg, 4, 2))[1];
        }

        return substr($jpeg, 0, $insertAt).$app2.substr($jpeg, $insertAt);
    }

    /**
     * Converts a simple or extended WebP into the extended layout with an
     * ICCP chunk (which must follow VP8X directly) and the ICC flag set.
     */
    public static function embedInWebp(string $webp, string $profile, int $width, int $height): string
    {
        if (substr($webp, 0, 4) !== 'RIFF' || substr($webp, 8, 4) !== 'WEBP') {
            return $webp;
        }

        $chunks = [];
        $offset = 12;

        while ($offset + 8 <= strlen($webp)) {
            $type = substr($webp, $offset, 4);
            $size = unpack('V', substr($webp, $offset + 4, 4))[1];
            $chunks[] = [$type, substr($webp, $offset + 8, $size)];
            $offset += 8 + $size + ($size % 2);
        }

        $flags = 0x20; // ICC profile present
        $body = '';

        foreach ($chunks as [$type, $data]) {
            if ($type === 'VP8X') {
                $flags |= ord($data[0]);

                continue;
            }

            if ($type === 'ICCP') {
                continue;
            }

            if ($type === 'VP8L' || $type === 'ALPH') {
                $flags |= 0x10; // may carry alpha
            }

            $body .= self::riffChunk($type, $data);
        }

        $vp8x = chr($flags)."\0\0\0"
            .substr(pack('V', $width - 1), 0, 3)
            .substr(pack('V', $height - 1), 0, 3);

        $payload = 'WEBP'.self::riffChunk('VP8X', $vp8x).self::riffChunk('ICCP', $profile).$body;

        return 'RIFF'.pack('V', strlen($payload)).$payload;
    }

    private static function riffChunk(string $type, string $data): string
    {
        return $type.pack('V', strlen($data)).$data.(strlen($data) % 2 ? "\0" : '');
    }
}

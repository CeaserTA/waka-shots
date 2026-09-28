<?php

namespace App\Support;

/**
 * Reads the EXIF orientation tag (1-8) from JPEG bytes.
 *
 * Browsers rotate a photo by this tag when showing the original, but GD
 * ignores it. Any copy re-encoded by GD (a downscaled master or a WebP
 * variant) must therefore be rotated by hand, or portrait phone shots come
 * out sideways. Parsed directly so it does not depend on PHP's exif
 * extension being installed.
 */
class ExifOrientation
{
    public static function fromJpeg(string $bytes): int
    {
        if (strncmp($bytes, "\xFF\xD8", 2) !== 0) {
            return 1;
        }

        $length = strlen($bytes);
        $offset = 2;

        while ($offset + 4 <= $length) {
            if ($bytes[$offset] !== "\xFF") {
                return 1;
            }

            $marker = ord($bytes[$offset + 1]);

            if ($marker === 0xFF) { // fill byte before a marker
                $offset++;

                continue;
            }

            if ($marker === 0xDA || $marker === 0xD9) { // image data or end: no metadata beyond this
                return 1;
            }

            $size = unpack('n', substr($bytes, $offset + 2, 2))[1];

            if ($marker === 0xE1 && substr($bytes, $offset + 4, 6) === "Exif\0\0") {
                return self::fromTiff(substr($bytes, $offset + 10, $size - 8));
            }

            $offset += 2 + $size;
        }

        return 1;
    }

    private static function fromTiff(string $tiff): int
    {
        [$short, $long] = match (substr($tiff, 0, 2)) {
            'II' => ['v', 'V'],
            'MM' => ['n', 'N'],
            default => [null, null],
        };

        if ($short === null || strlen($tiff) < 8) {
            return 1;
        }

        $ifd = unpack($long, substr($tiff, 4, 4))[1];

        if (strlen($tiff) < $ifd + 2) {
            return 1;
        }

        $entries = unpack($short, substr($tiff, $ifd, 2))[1];

        for ($i = 0; $i < $entries; $i++) {
            $entry = $ifd + 2 + $i * 12;

            if (strlen($tiff) < $entry + 12) {
                return 1;
            }

            if (unpack($short, substr($tiff, $entry, 2))[1] === 0x0112) {
                $value = unpack($short, substr($tiff, $entry + 8, 2))[1];

                return $value >= 1 && $value <= 8 ? $value : 1;
            }
        }

        return 1;
    }
}

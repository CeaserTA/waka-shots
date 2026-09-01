<?php

namespace App\Console\Commands;

use App\Models\PortfolioItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizePortfolioImages extends Command
{
    protected $signature = 'portfolio:optimize-images
        {--width=2500 : Maximum longest-edge dimension in pixels}
        {--quality=85 : JPEG output quality}';

    protected $description = 'Downscale oversized portfolio images stored on the r2 disk in place';

    public function handle(): int
    {
        $maxWidth = (int) $this->option('width');
        $quality = (int) $this->option('quality');
        $disk = Storage::disk('r2');

        $items = PortfolioItem::whereNotNull('image_path')->get();

        foreach ($items as $item) {
            $path = $item->image_path;

            if (! $disk->exists($path)) {
                $this->warn("Skipping #{$item->id}: {$path} not found on r2");

                continue;
            }

            $originalBytes = $disk->size($path);
            $contents = $disk->get($path);

            $image = @imagecreatefromstring($contents);

            if ($image === false) {
                $this->warn("Skipping #{$item->id}: {$path} could not be decoded as an image");

                continue;
            }

            $width = imagesx($image);
            $height = imagesy($image);

            if ($width <= $maxWidth) {
                imagedestroy($image);
                $this->line("#{$item->id}: {$path} already <= {$maxWidth}px wide, skipping");

                continue;
            }

            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);

            ob_start();
            imagejpeg($resized, null, $quality);
            $output = ob_get_clean();
            imagedestroy($resized);

            $disk->put($path, $output, 'public');

            $newBytes = strlen($output);

            $this->info(sprintf(
                '#%d: %s  %dx%d -> %dx%d, %.2fMB -> %.2fMB',
                $item->id,
                $path,
                $width,
                $height,
                $newWidth,
                $newHeight,
                $originalBytes / 1024 / 1024,
                $newBytes / 1024 / 1024,
            ));
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}

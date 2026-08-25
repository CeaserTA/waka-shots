<?php

namespace App\Console\Commands;

use App\Exceptions\DriveConnectionException;
use App\Services\DriveGalleryService;
use Illuminate\Console\Command;
use Throwable;

class DriveTestConnectionCommand extends Command
{
    protected $signature = 'drive:test-connection {folderId : The Google Drive folder ID}';

    protected $description = 'List image files in a Google Drive folder';

    public function handle(DriveGalleryService $drive): int
    {
        try {
            $images = $drive->listImagesInFolder($this->argument('folderId'));

            if ($images === []) {
                $this->info('No images found.');
                return self::SUCCESS;
            }

            foreach ($images as $image) {
                $this->line(sprintf('%s | %s | %s', $image['id'], $image['name'], $image['thumbnailLink'] ?? ''));
            }

            return self::SUCCESS;
        } catch (DriveConnectionException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error('Google Drive request failed: ' . $exception->getMessage());
            return self::FAILURE;
        }
    }
}

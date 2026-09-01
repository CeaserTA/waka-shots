<?php

namespace App\Jobs;

use App\Models\PortfolioItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UploadPortfolioImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public string $localPath,
        public int $categoryId,
    ) {}

    public function handle(): void
    {
        if (! Storage::disk('local')->exists($this->localPath)) {
            return;
        }

        $extension = pathinfo($this->localPath, PATHINFO_EXTENSION);
        $remotePath = 'portfolio-images/' . Str::uuid() . ($extension ? ".{$extension}" : '');

        $stream = Storage::disk('local')->readStream($this->localPath);
        Storage::disk('r2')->put($remotePath, $stream, 'public');

        if (is_resource($stream)) {
            fclose($stream);
        }

        PortfolioItem::create([
            'category_id' => $this->categoryId,
            'image_path' => $remotePath,
            'title' => null,
        ]);

        Storage::disk('local')->delete($this->localPath);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Portfolio bulk upload failed', [
            'path' => $this->localPath,
            'category_id' => $this->categoryId,
            'exception' => $exception->getMessage(),
        ]);
    }
}

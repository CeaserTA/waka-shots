<?php

namespace App\Jobs;

use App\Models\PortfolioItem;
use App\Services\PortfolioImageProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Runs after a portfolio image is uploaded or replaced: caps its size, records
 * its dimensions and writes the responsive WebP variants. Until it finishes,
 * the page simply shows the uploaded master as before.
 */
class ProcessPortfolioImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    // A 2500px upload takes a few seconds; stay under the queue's 90s
    // retry_after so a slow run is never picked up twice.
    public int $timeout = 80;

    public function __construct(public int $portfolioItemId) {}

    public function handle(PortfolioImageProcessor $processor): void
    {
        $item = PortfolioItem::find($this->portfolioItemId);

        if (! $item || ! PortfolioImageProcessor::isStoredOnDisk($item->image_path)) {
            return;
        }

        $result = $processor->process($item);

        if (($result['status'] ?? null) === 'error') {
            Log::warning('Portfolio image processing failed', ['item' => $item->id] + $result);
        }
    }
}

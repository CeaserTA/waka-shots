<?php

namespace App\Observers;

use App\Jobs\ProcessPortfolioImage;
use App\Models\PortfolioItem;
use App\Services\PortfolioImageProcessor;

class PortfolioItemObserver
{
    /**
     * A replaced image invalidates the old dimensions and variants; clear them
     * so the page falls back to the new master until it has been processed.
     */
    public function saving(PortfolioItem $item): void
    {
        if ($item->exists && $item->isDirty('image_path')) {
            $item->width = null;
            $item->height = null;
            $item->variant_widths = null;
        }
    }

    public function saved(PortfolioItem $item): void
    {
        if (! $item->wasRecentlyCreated && ! $item->wasChanged('image_path')) {
            return;
        }

        if (PortfolioImageProcessor::isStoredOnDisk($item->image_path)) {
            ProcessPortfolioImage::dispatch($item->id)->afterCommit();
        }
    }
}

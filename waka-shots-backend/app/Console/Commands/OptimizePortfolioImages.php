<?php

namespace App\Console\Commands;

use App\Models\PortfolioItem;
use App\Services\PortfolioImageProcessor;
use Illuminate\Console\Command;
use Throwable;

class OptimizePortfolioImages extends Command
{
    protected $signature = 'portfolio:optimize-images
        {--max-edge=2500 : Maximum longest-edge dimension in pixels}
        {--quality=85 : JPEG/WebP output quality}
        {--dry-run : Report what would change without writing anything}';

    protected $description = 'Downscale portfolio images whose longest edge exceeds the limit. Each smaller copy is written to a new key and the original object is kept untouched.';

    public function handle(PortfolioImageProcessor $processor): int
    {
        $maxEdge = (int) $this->option('max-edge');
        $quality = (int) $this->option('quality');
        $dryRun = (bool) $this->option('dry-run');

        $totals = ['optimized' => 0, 'skipped' => 0, 'errors' => 0, 'before' => 0, 'after' => 0];

        foreach (PortfolioItem::whereNotNull('image_path')->orderBy('id')->get() as $item) {
            try {
                $result = $processor->downscale($item, $maxEdge, $quality, $dryRun);
            } catch (Throwable $e) {
                $result = ['status' => 'error', 'reason' => $e->getMessage()];
            }

            match ($result['status']) {
                'optimized', 'would-optimize' => $this->reportOptimized($item, $result, $totals),
                'error' => $this->reportError($item, $result, $totals),
                default => $this->reportSkipped($item, $result, $totals),
            };

            // A new master needs new variants; build them now rather than
            // leaving the page on the full-size fallback.
            if ($result['status'] === 'optimized') {
                try {
                    $variants = $processor->generateVariants($item->refresh(), force: true);
                } catch (Throwable $e) {
                    $variants = ['status' => 'error', 'reason' => $e->getMessage()];
                }

                if ($variants['status'] === 'error') {
                    $this->warn("  variants for #{$item->id} failed: {$variants['reason']}");
                }
            }
        }

        $this->newLine();
        $this->info(sprintf(
            '%s %d, skipped %d, errors %d. Optimized files: %.2fMB -> %.2fMB (%.2fMB saved).',
            $dryRun ? 'Would optimize' : 'Optimized',
            $totals['optimized'],
            $totals['skipped'],
            $totals['errors'],
            $totals['before'] / 1048576,
            $totals['after'] / 1048576,
            ($totals['before'] - $totals['after']) / 1048576,
        ));

        if (! $dryRun && $totals['optimized'] > 0) {
            $this->line('The original objects were kept at their old keys; nothing was deleted.');
        }

        return $totals['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function reportOptimized(PortfolioItem $item, array $result, array &$totals): void
    {
        $totals['optimized']++;
        $totals['before'] += $result['bytes_before'];
        $totals['after'] += $result['bytes_after'];

        $this->info(sprintf(
            '#%d: %s %dx%d -> %dx%d, %.2fMB -> %.2fMB, now %s',
            $item->id,
            $result['path'],
            $result['from'][0],
            $result['from'][1],
            $result['to'][0],
            $result['to'][1],
            $result['bytes_before'] / 1048576,
            $result['bytes_after'] / 1048576,
            $result['new_path'],
        ));
    }

    private function reportSkipped(PortfolioItem $item, array $result, array &$totals): void
    {
        $totals['skipped']++;

        if ($this->output->isVerbose()) {
            $this->line("#{$item->id}: skipped, {$result['reason']}");
        }
    }

    private function reportError(PortfolioItem $item, array $result, array &$totals): void
    {
        $totals['errors']++;
        $this->warn("#{$item->id}: {$result['reason']}");
    }
}

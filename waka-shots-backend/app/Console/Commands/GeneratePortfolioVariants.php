<?php

namespace App\Console\Commands;

use App\Models\PortfolioItem;
use App\Services\PortfolioImageProcessor;
use Illuminate\Console\Command;
use Throwable;

class GeneratePortfolioVariants extends Command
{
    protected $signature = 'portfolio:generate-variants
        {--force : Regenerate items that already have variants}
        {--id=* : Only these portfolio item IDs}';

    protected $description = 'Record dimensions, write responsive WebP variants and set long-lived cache headers for portfolio images.';

    public function handle(PortfolioImageProcessor $processor): int
    {
        $query = PortfolioItem::whereNotNull('image_path')->orderBy('id');

        if ($ids = $this->option('id')) {
            $query->whereIn('id', $ids);
        }

        $counts = ['generated' => 0, 'skipped' => 0, 'errors' => 0];
        $masterBytes = 0;
        $variantBytes = 0;

        foreach ($query->get() as $item) {
            try {
                $result = $processor->generateVariants($item, (bool) $this->option('force'));
            } catch (Throwable $e) {
                $result = ['status' => 'error', 'reason' => $e->getMessage()];
            }

            if ($result['status'] === 'generated') {
                $counts['generated']++;
                $masterBytes += $result['master_bytes'];
                $variantBytes += array_sum($result['variant_bytes']);

                $this->line(sprintf(
                    '#%d: %dx%d, variants %s%s',
                    $item->id,
                    $result['size'][0],
                    $result['size'][1],
                    collect($result['variant_bytes'])->map(fn ($bytes, $width) => $width.'w '.round($bytes / 1024).'KB')->implode(', '),
                    $result['cache_header'] ? '' : ' (cache header not applied: not an S3 disk)',
                ));
            } elseif ($result['status'] === 'error') {
                $counts['errors']++;
                $this->warn("#{$item->id}: {$result['reason']}");
            } else {
                $counts['skipped']++;
            }
        }

        $this->newLine();
        $this->info(sprintf(
            'Generated %d, skipped %d, errors %d. Masters %.1fMB, all variants %.1fMB.',
            $counts['generated'],
            $counts['skipped'],
            $counts['errors'],
            $masterBytes / 1048576,
            $variantBytes / 1048576,
        ));

        return $counts['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}

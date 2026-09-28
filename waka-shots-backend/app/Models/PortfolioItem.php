<?php

namespace App\Models;

use App\Services\PortfolioImageProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PortfolioItem extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'alt_text',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'variant_widths' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Whether the item has its own title. Bulk-uploaded items start without one,
     * and their caption falls back to the category name.
     */
    public function hasTitle(): bool
    {
        return filled($this->title);
    }

    /**
     * Short visible caption: the title, else the category name.
     */
    public function displayCaption(): string
    {
        return $this->hasTitle() ? $this->title : ($this->category?->name ?? 'Portfolio');
    }

    /**
     * Image alt attribute: the written description if there is one, else the
     * same text as the caption. Alt text and caption differ on purpose — alt
     * describes what is in the photo, the caption is a short display title.
     */
    public function displayAlt(): string
    {
        return filled($this->alt_text) ? $this->alt_text : $this->displayCaption();
    }

    /** URL of the stored master image (older items may hold a full external URL). */
    public function imageUrl(): string
    {
        return PortfolioImageProcessor::isStoredOnDisk($this->image_path)
            ? Storage::disk('r2')->url($this->image_path)
            : (string) $this->image_path;
    }

    /** Whether both dimensions are known, so the page can reserve the photo's space. */
    public function hasDimensions(): bool
    {
        return $this->width > 0 && $this->height > 0;
    }

    /**
     * WebP srcset ("url 480w, url 800w, …"), or null until the variants exist.
     */
    public function srcset(): ?string
    {
        $widths = $this->variantWidths();

        if ($widths === []) {
            return null;
        }

        return collect($widths)
            ->map(fn (int $width) => $this->variantUrl($width)." {$width}w")
            ->implode(', ');
    }

    /**
     * Image for the lightbox: the largest variant whose longest side stays
     * within the lightbox size, falling back to the master until variants exist.
     */
    public function lightboxUrl(): string
    {
        $widths = $this->variantWidths();

        if ($widths === [] || ! $this->hasDimensions()) {
            return $this->imageUrl();
        }

        // 10% leeway: variantWidths() folds a lightbox copy into a grid width
        // up to 10% larger, and that larger copy should still be picked.
        $limit = PortfolioImageProcessor::lightboxWidth($this->width, $this->height) * 1.1;
        $fitting = array_filter($widths, fn (int $width) => $width <= $limit);

        return $this->variantUrl($fitting === [] ? min($widths) : max($fitting));
    }

    private function variantUrl(int $width): string
    {
        return Storage::disk('r2')->url(PortfolioImageProcessor::variantPath($this->image_path, $width));
    }

    /** @return list<int> */
    private function variantWidths(): array
    {
        if (! PortfolioImageProcessor::isStoredOnDisk($this->image_path) || ! is_array($this->variant_widths)) {
            return [];
        }

        return array_values(array_map('intval', $this->variant_widths));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'alt_text',
        'image_path',
    ];

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
}

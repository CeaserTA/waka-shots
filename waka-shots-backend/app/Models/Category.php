<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    public function journalPosts(): HasMany
    {
        return $this->hasMany(JournalPost::class);
    }

    public function films(): HasMany
    {
        return $this->hasMany(Film::class);
    }
}

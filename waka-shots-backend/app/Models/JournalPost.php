<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalPost extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'thumbnail_path',
        'is_published',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // The admin form pre-fills the slug from the title; this covers posts
        // created without one (or with a colliding one left blank).
        static::creating(function (JournalPost $post): void {
            if (blank($post->slug)) {
                $post->slug = static::uniqueSlugFor($post->title);
            }
        });
    }

    public static function uniqueSlugFor(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;

        for ($suffix = 2; static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists(); $suffix++) {
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags((string) $this->content));

        return max(1, (int) ceil($words / 200));
    }

    public function thumbnailUrl(): ?string
    {
        if (blank($this->thumbnail_path)) {
            return null;
        }

        return Str::startsWith($this->thumbnail_path, ['http://', 'https://'])
            ? $this->thumbnail_path
            : Storage::disk('r2')->url($this->thumbnail_path);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Plain-text summary of the rich-text body, for meta descriptions and cards.
     */
    public function excerpt(int $limit = 155): string
    {
        // Drop non-prose elements with their contents, put a space at block
        // boundaries so "</p><p>" doesn't glue paragraphs together, then strip
        // inline tags without adding space (so "<a>venue</a>." stays "venue.").
        $html = preg_replace('#<(script|style|template)\b[^>]*>.*?</\1>#is', ' ', (string) $this->content);
        $html = preg_replace('#<(/?(p|div|h[1-6]|li|ul|ol|blockquote|figure|figcaption|table|tr|td|th|pre|hr)\b[^>]*|br\s*/?)>#i', ' ', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::of(str_replace("\u{00A0}", ' ', $text))->squish()->limit($limit)->toString();
    }
}

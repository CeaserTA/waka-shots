<?php

namespace App\Models;

use App\Support\WhatsApp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    protected $fillable = [
        'studio_name',
        'contact_email',
        'contact_phone',
        'whatsapp_number',
        'address',
        'instagram_url',
        'youtube_url',
        'facebook_url',
        'tiktok_url',
        'x_url',
        'hero_tagline',
        'home_hero_image',
        'home_partners_image',
        'footer_about_text',
        'portfolio_hero_image',
        'portfolio_hero_eyebrow',
        'portfolio_hero_heading',
        'contact_image',
        'contact_tagline',
        'photographer_image',
        'photographer_heading',
        'photographer_bio',
        'story_heading',
        'story_text',
        'story_image',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'studio_name' => 'Waka Shots',
        ]);
    }

    /**
     * Click-to-chat link for the studio's WhatsApp number, or null when no
     * number is set so callers can skip rendering.
     */
    public function whatsappLink(?string $message = null): ?string
    {
        return WhatsApp::link($this->whatsapp_number, $message);
    }

    public function imageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('r2')->url($path);
    }
}

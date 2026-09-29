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
        'google_review_url',
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
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Fallbacks for every key in the `content` column. A blank value in the
     * admin means "use the default", so the site never renders an empty
     * heading. The admin form shows these as placeholders.
     */
    public const CONTENT_DEFAULTS = [
        'site' => [
            'title' => 'Waka Shots Photography',
            'description' => 'Kampala-based photography studio for weddings, introduction ceremonies, portraits, graduations and brand campaigns. Patience over performance — every session shaped around your story.',
            'footer_tagline' => 'Photography · Weddings · Portraits · Events · Brands',
            'response_time' => 'Within 24–48 hours',
            'whatsapp_message' => "Hi Waka Shots! I'd love to enquire about booking a photography session with you.",
            'gallery_whatsapp_message' => 'Hi Waka Shots! I have a question about my gallery.',
        ],
        'stats' => [
            ['value' => '120+', 'label' => 'Stories Told'],
            ['value' => '7', 'label' => 'Years Behind the Lens'],
            ['value' => '5', 'label' => 'Countries Shot In'],
        ],
        'home' => [
            'hero_eyebrow' => 'Waka Shots Photography — Kampala, Uganda',
            'hero_text' => 'We make timeless, intentional imagery for {words} across East Africa — one honest frame at a time.',
            'hero_words' => ['weddings', 'portraits', 'graduations', 'brand stories'],
            'hero_tags' => ['f/1.8', '85mm', 'Kampala, UG', 'Golden Hour', '35mm'],
            'work_eyebrow' => 'Selected Work',
            'work_heading' => 'A closer look at recent frames.',
            'about_eyebrow' => 'About Waka Shots',
            'services_eyebrow' => 'Services',
            'services_heading' => 'Ways to work with us.',
            'partners_eyebrow' => 'Trusted By',
            'partners_heading' => 'Our Partners',
            'testimonials_eyebrow' => 'Client Stories',
            'testimonials_heading' => 'Told in their own words.',
            'journal_eyebrow' => 'Journal',
            'journal_heading' => 'Notes from behind the lens.',
            'cta_eyebrow' => "Let's Talk",
            'cta_heading' => 'Ready to create something unforgettable?',
        ],
        'about' => [
            'meta_description' => 'Meet Waka Shots, a Kampala photography studio built on patience over performance. Weddings, portraits, events and brand work across Uganda and beyond.',
            'hero_image' => 'https://images.unsplash.com/photo-1567531708788-4c44105d00ff?auto=format&fit=crop&w=1800&q=80',
            'hero_eyebrow' => 'Our Story',
            'hero_heading' => 'About Waka Shots',
            'story_eyebrow' => 'Who We Are',
            'values_eyebrow' => 'What We Believe',
            'values_heading' => 'Four principles behind everything we shoot.',
            'values' => [
                ['title' => 'Patience over performance', 'description' => "We'd rather wait for the real moment than manufacture a fake one."],
                ['title' => 'Light tells the truth', 'description' => 'We build every shoot around natural, honest light rather than forcing a look.'],
                ['title' => 'Story before spectacle', 'description' => 'A quiet, honest frame will always outlast a flashy one.'],
                ['title' => 'Care in the details', 'description' => 'From first email to final gallery, every step is handled with the same intention.'],
            ],
            'cta_eyebrow' => "Let's Work Together",
            'cta_heading' => 'Ready to create something unforgettable?',
        ],
        'services' => [
            'meta_description' => 'Photography services and starting packages from Waka Shots in Kampala. Every project begins with a conversation, and scope and pricing are shaped around your story.',
            'hero_image' => 'https://images.unsplash.com/photo-1565884280295-98eb83e41c65?auto=format&fit=crop&w=1800&q=80',
            'hero_eyebrow' => 'What We Offer',
            'hero_heading' => 'Services',
            'intro' => 'Every project starts as a conversation, not a package. The tiers below are starting points — the scope, timeline and pricing are always shaped around what your story actually needs.',
            'process_eyebrow' => 'How We Work',
            'process_heading' => 'Four steps, in order, every time.',
            'process' => [
                ['title' => 'Discover', 'description' => 'A conversation — in person or on a call — to understand your story, your day and what matters most to capture.'],
                ['title' => 'Plan', 'description' => 'We map locations, light and timing, so the day runs smoothly and nothing important is left to chance.'],
                ['title' => 'Create', 'description' => 'On the day, we work quietly and attentively — present enough to catch what actually happens.'],
                ['title' => 'Deliver', 'description' => 'A curated, edited gallery delivered within two to four weeks, ready to keep and share.'],
            ],
            'cta_eyebrow' => 'Not Sure Which Fits?',
            'cta_heading' => "Tell us about your project — we'll guide you from there.",
        ],
        'portfolio' => [
            'meta_description' => 'Selected work from Waka Shots: weddings, introduction ceremonies, portraits, graduations and brand campaigns photographed in Kampala and across Uganda.',
            'cta_eyebrow' => 'Like What You See?',
            'cta_heading' => "Let's plan your own session.",
        ],
        'films' => [
            'meta_description' => 'Watch Waka Shots films: highlight reels, behind-the-scenes footage and full ceremony films from our YouTube channel, playable right here.',
            'hero_image' => 'https://images.unsplash.com/photo-1608009232260-9b527a5bb9bd?auto=format&fit=crop&w=1800&q=80',
            'hero_eyebrow' => 'On the Channel',
            'hero_heading' => 'Films',
            'intro' => 'Beyond the still frame — highlight reels, behind-the-scenes footage and full ceremony films from our YouTube channel. Press play to watch right here.',
            'cta_eyebrow' => 'Watch More',
            'cta_heading' => 'More films live on our YouTube channel.',
        ],
        'journal' => [
            'meta_description' => 'Notes from the Waka Shots studio in Kampala: stories and behind-the-scenes moments from the weddings, portraits and projects we photograph.',
            'hero_image' => 'https://images.unsplash.com/photo-1633150747731-c945ec51b663?auto=format&fit=crop&w=1800&q=80',
            'hero_eyebrow' => 'Notes From the Studio',
            'hero_heading' => 'Journal',
            'cta_eyebrow' => 'Enjoyed These?',
            'cta_heading' => "Let's write your story next.",
        ],
        'contact' => [
            'meta_description' => 'Book a session or ask a question. Get in touch with Waka Shots, a Kampala photography studio. We reply to every enquiry within 24–48 hours.',
            'hero_image' => 'https://images.unsplash.com/photo-1708170236215-b6edcad7f49a?auto=format&fit=crop&w=1800&q=80',
            'hero_eyebrow' => "Let's Talk",
        ],
    ];

    /** Default for a dotted `content` key, e.g. "about.hero_heading". */
    public static function contentDefault(string $key): mixed
    {
        return data_get(static::CONTENT_DEFAULTS, $key);
    }

    /**
     * Admin-edited value for a dotted `content` key, falling back to the
     * default when it is blank. Lists come back re-indexed and with empty
     * rows dropped.
     */
    public function content(string $key): mixed
    {
        $value = data_get($this->content, $key);

        if (is_array($value)) {
            $value = array_values(array_filter($value, fn ($item): bool => is_array($item)
                ? collect($item)->contains(fn ($field): bool => filled($field))
                : filled($item)));
        }

        return filled($value) ? $value : static::contentDefault($key);
    }

    /** Page title in the site's "Page — Studio" format. */
    public function pageTitle(?string $page = null): string
    {
        $title = $this->content('site.title');

        return $page === null ? $title : "{$page} — {$title}";
    }

    /** Image URL for a dotted `content` key, falling back to the default image. */
    public function contentImageUrl(string $key): ?string
    {
        return $this->imageUrl(data_get($this->content, $key)) ?? static::contentDefault($key);
    }

    /**
     * Split admin text into paragraphs on blank lines.
     *
     * @return list<string>
     */
    public static function paragraphs(?string $text): array
    {
        if (blank($text)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', preg_split('/\n\s*\n/', trim($text))),
            'filled',
        ));
    }

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

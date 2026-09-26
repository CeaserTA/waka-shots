<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\JournalPost;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private const DEFAULT_DESCRIPTION = 'Kampala-based photography studio for weddings, introduction ceremonies, portraits, graduations and brand campaigns. Patience over performance — every session shaped around your story.';

    public function test_pages_get_default_description_social_tags_and_canonical(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<meta name="description" content="'.e(self::DEFAULT_DESCRIPTION).'">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:url" content="'.route('about').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<link rel="canonical" href="'.route('about').'">', false);
    }

    public function test_image_tags_are_omitted_without_a_hero_image_and_present_with_one(): void
    {
        SiteSetting::current()->update(['home_hero_image' => null]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('og:image', false)
            ->assertDontSee('twitter:image', false);

        SiteSetting::current()->update(['home_hero_image' => 'https://cdn.example.test/hero.jpg']);

        $this->get(route('home'))
            ->assertSee('<meta property="og:image" content="https://cdn.example.test/hero.jpg">', false)
            ->assertSee('<meta name="twitter:image" content="https://cdn.example.test/hero.jpg">', false);
    }

    public function test_local_business_schema_uses_only_real_values(): void
    {
        SiteSetting::current()->update([
            'studio_name' => 'Waka Shots',
            'contact_phone' => '+256 700 000000',
            'contact_email' => null,
            'address' => 'Kampala, Uganda',
            'instagram_url' => 'https://instagram.com/wakashots',
            'facebook_url' => null,
            'x_url' => 'https://x.com/wakashots',
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $match);
        $schema = json_decode($match[1] ?? '', true);

        $this->assertIsArray($schema, 'JSON-LD block must be valid JSON');
        $this->assertSame('LocalBusiness', $schema['@type']);
        $this->assertSame('Waka Shots', $schema['name']);
        $this->assertSame('Kampala, Uganda', $schema['address']);
        $this->assertSame(['https://instagram.com/wakashots', 'https://x.com/wakashots'], $schema['sameAs']);
        $this->assertArrayNotHasKey('email', $schema);
        $this->assertArrayNotHasKey('geo', $schema);
    }

    public function test_analytics_only_renders_when_configured(): void
    {
        config(['services.google_analytics.id' => null]);
        $this->get(route('home'))->assertDontSee('googletagmanager.com', false);

        config(['services.google_analytics.id' => 'G-TEST123']);
        $this->get(route('home'))
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123', false)
            ->assertSee("gtag('config', \"G-TEST123\")", false);
    }

    public function test_sitemap_lists_public_pages_and_nothing_private(): void
    {
        $category = Category::create(['name' => 'Journal', 'slug' => 'journal']);
        JournalPost::create(['category_id' => $category->id, 'title' => 'Draft', 'content' => 'x', 'is_published' => false]);
        JournalPost::create(['category_id' => $category->id, 'title' => 'Live', 'content' => 'x', 'is_published' => true]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $xml = $response->getContent();

        $this->assertNotFalse(simplexml_load_string($xml), 'sitemap must be valid XML');
        foreach (['home', 'portfolio', 'services', 'films', 'about', 'contact', 'journal'] as $route) {
            $this->assertStringContainsString('<loc>'.route($route).'</loc>', $xml);
        }
        $this->assertStringNotContainsString('/gallery', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
    }

    public function test_robots_blocks_admin_and_galleries_and_points_to_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Disallow: /gallery/', $robots);
        $this->assertMatchesRegularExpression('#^Sitemap: https://\S+/sitemap\.xml$#m', $robots);
    }
}

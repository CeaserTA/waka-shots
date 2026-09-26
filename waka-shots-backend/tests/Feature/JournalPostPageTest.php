<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\JournalPost;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JournalPostPageTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $attributes = []): JournalPost
    {
        $category = Category::firstOrCreate(['slug' => 'weddings'], ['name' => 'Weddings']);

        return JournalPost::create($attributes + [
            'category_id' => $category->id,
            'title' => 'A Quiet Morning in Entebbe',
            'is_published' => true,
            'content' => '<p>We arrived at six to catch the first light over the lake.</p>',
        ]);
    }

    public function test_new_posts_get_a_unique_slug_from_their_title(): void
    {
        $first = $this->makePost();
        $second = $this->makePost();
        $custom = $this->makePost(['title' => 'Anything', 'slug' => 'my-own-slug']);

        $this->assertSame('a-quiet-morning-in-entebbe', $first->slug);
        $this->assertSame('a-quiet-morning-in-entebbe-2', $second->slug);
        $this->assertSame('my-own-slug', $custom->slug);
    }

    public function test_editing_the_title_keeps_the_existing_slug(): void
    {
        $post = $this->makePost();
        $post->update(['title' => 'A Renamed Story']);

        $this->assertSame('a-quiet-morning-in-entebbe', $post->fresh()->slug);
    }

    public function test_post_page_shows_the_post_with_its_own_meta_tags(): void
    {
        $post = $this->makePost(['content' => '<h2>Before the vows</h2><p>First light over the lake, <a href="https://example.com/venue">the venue</a>.</p><script>alert(1)</script>']);
        $other = $this->makePost(['title' => 'Graduation Day', 'content' => '<p>Caps in the air.</p>']);

        $html = $this->get(route('journal.show', $post->slug))->assertOk()->getContent();

        $this->assertStringContainsString('<title>A Quiet Morning in Entebbe — Waka Shots Photography</title>', $html);
        $this->assertStringContainsString('<meta name="description" content="Before the vows First light over the lake, the venue.">', $html);
        $this->assertStringContainsString('<meta property="og:title" content="A Quiet Morning in Entebbe — Waka Shots Photography">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="'.route('journal.show', $post->slug).'">', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.route('journal.show', $post->slug).'">', $html);
        $this->assertStringContainsString('href="https://example.com/venue"', $html);
        $this->assertStringNotContainsString('alert(1)', $html);
        $this->assertStringNotContainsString('Caps in the air.', $html);

        $this->get(route('journal.show', $other->slug))
            ->assertSee('<title>Graduation Day — Waka Shots Photography</title>', false)
            ->assertSee('<meta name="description" content="Caps in the air.">', false);
    }

    public function test_thumbnail_is_shown_and_used_as_the_share_image(): void
    {
        SiteSetting::current()->update(['home_hero_image' => 'https://cdn.example.test/hero.jpg']);
        $withThumb = $this->makePost(['thumbnail_path' => 'https://cdn.example.test/entebbe.jpg']);
        $withoutThumb = $this->makePost(['title' => 'No Picture Yet']);

        $this->get(route('journal.show', $withThumb->slug))
            ->assertOk()
            ->assertSee('<img src="https://cdn.example.test/entebbe.jpg"', false)
            ->assertSee('<meta property="og:image" content="https://cdn.example.test/entebbe.jpg">', false)
            ->assertSee('<meta name="twitter:image" content="https://cdn.example.test/entebbe.jpg">', false);

        $this->get(route('journal.show', $withoutThumb->slug))
            ->assertOk()
            ->assertSee('<meta property="og:image" content="https://cdn.example.test/hero.jpg">', false);

        $this->get(route('journal'))
            ->assertSee('<img src="https://cdn.example.test/entebbe.jpg"', false)
            ->assertSee('Waka Shots Journal');
    }

    public function test_home_page_journal_teaser_shows_thumbnails_and_links_to_posts(): void
    {
        $post = $this->makePost(['thumbnail_path' => 'https://cdn.example.test/entebbe.jpg']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('journal.show', $post->slug).'"', false)
            ->assertSee('<img src="https://cdn.example.test/entebbe.jpg"', false);
    }

    public function test_drafts_and_unknown_slugs_are_not_found(): void
    {
        $draft = $this->makePost(['title' => 'Draft notes', 'is_published' => false]);

        $this->get(route('journal.show', $draft->slug))->assertNotFound();
        $this->get(route('journal.show', 'does-not-exist'))->assertNotFound();
    }

    public function test_journal_index_links_to_each_post(): void
    {
        $post = $this->makePost();

        $this->get(route('journal'))
            ->assertOk()
            ->assertSee('href="'.route('journal.show', $post->slug).'"', false);
    }

    public function test_migration_backfills_unique_slugs_for_existing_posts(): void
    {
        $category = Category::create(['name' => 'Weddings', 'slug' => 'weddings']);
        $migration = require database_path('migrations/2026_09_26_140000_add_slug_to_journal_posts_table.php');

        // Recreate the pre-migration state: posts exist, no slug column yet.
        $migration->down();
        foreach (['Same Title', 'Same Title', 'Other'] as $title) {
            DB::table('journal_posts')->insert([
                'category_id' => $category->id,
                'title' => $title,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $migration->up();

        $this->assertSame(
            ['same-title', 'same-title-2', 'other'],
            DB::table('journal_posts')->orderBy('id')->pluck('slug')->all(),
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Film;
use App\Models\JournalPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalFilmContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_journal_posts_can_store_content_and_published_status(): void
    {
        $category = Category::create([
            'name' => 'Journal',
            'slug' => 'journal',
        ]);

        $journalPost = JournalPost::create([
            'category_id' => $category->id,
            'title' => 'A day in the studio',
            'is_published' => true,
            'content' => '<p>We built something new.</p>',
        ]);

        $this->assertDatabaseHas('journal_posts', [
            'id' => $journalPost->id,
            'category_id' => $category->id,
            'title' => 'A day in the studio',
            'is_published' => true,
            'content' => '<p>We built something new.</p>',
        ]);
    }

    public function test_journal_only_renders_published_admin_posts(): void
    {
        $category = Category::create([
            'name' => 'Journal',
            'slug' => 'journal',
        ]);

        JournalPost::create([
            'category_id' => $category->id,
            'title' => 'Published studio notes',
            'is_published' => true,
            'content' => '<p>Published content.</p>',
        ]);

        JournalPost::create([
            'category_id' => $category->id,
            'title' => 'Draft studio notes',
            'is_published' => false,
            'content' => '<p>Draft content.</p>',
        ]);

        $response = $this->get(route('journal'));

        $response->assertOk()
            ->assertSee('Published studio notes')
            ->assertDontSee('Draft studio notes')
            ->assertDontSee('Behind the Lens: A Kampala Wedding');
    }

    public function test_films_can_store_youtube_ids_for_categories(): void
    {
        $category = Category::create([
            'name' => 'Brand Films',
            'slug' => 'brand-films',
        ]);

        $film = Film::create([
            'category_id' => $category->id,
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'category_id' => $category->id,
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);
    }
}

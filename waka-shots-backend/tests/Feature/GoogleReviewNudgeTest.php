<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\SiteSetting;
use App\Services\DriveGalleryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GoogleReviewNudgeTest extends TestCase
{
    use RefreshDatabase;

    private const REVIEW_URL = 'https://g.page/r/Ca1C7ZPGE70tEBM/review';

    private function gallery(): Gallery
    {
        $drive = Mockery::mock(DriveGalleryService::class);
        $drive->shouldReceive('listImagesInFolder')->andReturn([]);
        $this->app->instance(DriveGalleryService::class, $drive);

        return Gallery::create([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
            'expires_at' => now()->addWeek(),
        ]);
    }

    private function submitReview(Gallery $gallery, int $rating)
    {
        return $this->followingRedirects()->post(route('gallery.testimonial', $gallery->access_token), [
            'rating' => $rating,
            'quote' => 'Honest feedback about the whole experience.',
        ]);
    }

    public function test_prompt_shows_after_a_one_star_submission(): void
    {
        SiteSetting::current()->update(['google_review_url' => self::REVIEW_URL]);

        $this->submitReview($this->gallery(), 1)
            ->assertOk()
            ->assertSee('Thanks for your review.')
            ->assertSee('A quick Google review helps other clients find us too.')
            ->assertSee('<a href="'.self::REVIEW_URL.'" target="_blank" rel="noopener"', false);
    }

    public function test_prompt_shows_after_a_five_star_submission(): void
    {
        SiteSetting::current()->update(['google_review_url' => self::REVIEW_URL]);

        $this->submitReview($this->gallery(), 5)
            ->assertOk()
            ->assertSee('Thanks for your review.')
            ->assertSee('<a href="'.self::REVIEW_URL.'" target="_blank" rel="noopener"', false);
    }

    public function test_prompt_does_not_come_back_on_a_later_visit(): void
    {
        SiteSetting::current()->update(['google_review_url' => self::REVIEW_URL]);
        $gallery = $this->gallery();

        $this->submitReview($gallery, 4)->assertSee(self::REVIEW_URL, false);

        $this->get(route('gallery.show', $gallery->access_token))
            ->assertOk()
            ->assertDontSee('Thanks for your review.')
            ->assertDontSee(self::REVIEW_URL, false);
    }

    public function test_no_prompt_or_broken_link_when_the_review_url_is_not_set(): void
    {
        SiteSetting::current()->update(['google_review_url' => null]);

        $html = $this->submitReview($this->gallery(), 5)
            ->assertOk()
            ->assertSee('Thanks for your review.')
            ->assertDontSee('A quick Google review helps other clients find us too.')
            ->assertDontSee('Review us on Google')
            ->assertDontSee('data-google-review', false)
            ->getContent();

        // Without the link, the thank-you toast keeps its normal auto-dismiss.
        preg_match('#<div id="review-success-toast"[^>]*>#', $html, $toast);
        $this->assertNotEmpty($toast);
        $this->assertStringNotContainsString('data-keep-open', $toast[0]);
    }
}

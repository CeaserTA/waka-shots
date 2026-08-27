<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

class TestimonialSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_submission_is_pending_and_gallery_shows_thank_you_message(): void
    {
        $gallery = $this->gallery();

        $this->post(route('gallery.testimonial', $gallery->access_token), [
            'rating' => 5,
            'quote' => 'The photos were beautiful and the whole experience was wonderful.',
        ])->assertRedirect(route('gallery.show', $gallery->access_token));

        $this->assertDatabaseHas('testimonials', [
            'gallery_id' => $gallery->id,
            'rating' => 5,
            'status' => 'pending',
            'is_featured' => false,
        ]);

        $drive = Mockery::mock(\App\Services\DriveGalleryService::class);
        $drive->shouldReceive('listImagesInFolder')->once()->andReturn([]);
        $this->app->instance(\App\Services\DriveGalleryService::class, $drive);

        $this->get(route('gallery.show', $gallery->access_token))
            ->assertSee('Thanks for your review.')
            ->assertDontSee('Loved your photos? Leave a review.')
            ->assertDontSee('Submit review');
    }

    public function test_second_submission_for_gallery_is_blocked(): void
    {
        $gallery = $this->gallery();
        Testimonial::create([
            'gallery_id' => $gallery->id,
            'quote' => 'Already submitted review.',
            'rating' => 4,
        ]);

        $this->post(route('gallery.testimonial', $gallery->access_token), [
            'rating' => 1,
            'quote' => 'This second review must be rejected.',
        ])->assertRedirect(route('gallery.show', $gallery->access_token));

        $this->assertDatabaseHas('testimonials', [
            'gallery_id' => $gallery->id,
            'quote' => 'Already submitted review.',
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('testimonials', ['quote' => 'This second review must be rejected.']);
    }

    public function test_rejected_review_can_be_resubmitted_without_creating_a_duplicate(): void
    {
        $gallery = $this->gallery();
        $testimonial = Testimonial::create([
            'gallery_id' => $gallery->id,
            'quote' => 'This review was rejected.',
            'rating' => 2,
            'status' => 'rejected',
        ]);

        $this->post(route('gallery.testimonial', $gallery->access_token), [
            'rating' => 5,
            'quote' => 'The revised review is much better.',
        ])->assertRedirect(route('gallery.show', $gallery->access_token));

        $this->assertSame(1, Testimonial::where('gallery_id', $gallery->id)->count());
        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'quote' => 'The revised review is much better.',
            'status' => 'pending',
            'is_featured' => false,
        ]);
    }

    public function test_invalid_submission_does_not_create_a_testimonial(): void
    {
        $gallery = $this->gallery();

        $this->from(route('gallery.show', $gallery->access_token))
            ->post(route('gallery.testimonial', $gallery->access_token), [
                'rating' => 6,
                'quote' => 'Too short',
            ])
            ->assertRedirect(route('gallery.show', $gallery->access_token))
            ->assertSessionHasErrors(['rating', 'quote']);

        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_inactive_gallery_cannot_submit_a_review_or_render_the_form(): void
    {
        $gallery = $this->gallery(['is_active' => false]);

        $this->get(route('gallery.show', $gallery->access_token))
            ->assertSee('This gallery is no longer available.')
            ->assertDontSee('Loved your photos? Leave a review.');

        $this->post(route('gallery.testimonial', $gallery->access_token), [
            'rating' => 5,
            'quote' => 'This should not be accepted.',
        ])->assertRedirect(route('gallery.show', $gallery->access_token));

        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_testimonial_route_uses_its_rate_limiter(): void
    {
        $route = Route::getRoutes()->getByName('gallery.testimonial');

        $this->assertContains('throttle:gallery-testimonials', $route->middleware());
    }

    private function gallery(array $attributes = []): Gallery
    {
        return Gallery::create(array_merge([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ], $attributes));
    }
}

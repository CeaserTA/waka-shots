<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomepageTestimonialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_testimonials_section_is_hidden_when_no_approved_featured_reviews_exist(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Client Stories');
    }

    public function test_homepage_renders_up_to_six_testimonials_in_sort_order(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Amina Client',
            'event_name' => 'Wedding Kampala',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ]);

        foreach (range(1, 7) as $sortOrder) {
            $testimonialGallery = $sortOrder === 1 ? $gallery : Gallery::create([
                'client_name' => "Client {$sortOrder}",
                'event_name' => "Event {$sortOrder}",
                'event_date' => '2026-09-01',
                'drive_folder_id' => "folder_{$sortOrder}",
            ]);

            Testimonial::create([
                'gallery_id' => $testimonialGallery->id,
                'quote' => "Approved review {$sortOrder} with enough words.",
                'rating' => $sortOrder % 5 + 1,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => $sortOrder,
            ]);
        }

        Storage::disk('public')->put('testimonials/client.jpg', 'photo');
        Testimonial::where('gallery_id', $gallery->id)->update(['photo_path' => 'testimonials/client.jpg']);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Client Stories')
            ->assertSee('Amina Client')
            ->assertSee('Wedding Kampala')
            ->assertSee(Storage::disk('r2')->url('testimonials/client.jpg'))
            ->assertSee('bg-charcoal');

        $this->assertSame(6, substr_count($response->getContent(), 'spotlight-card min-w-0 w-full max-w-[420px] aspect-square'));
        $this->assertSame(6, substr_count($response->getContent(), 'border border-line bg-black'));
        $this->assertStringContainsString('testimonial-carousel-prev', $response->getContent());
        $this->assertStringContainsString('testimonial-carousel-next', $response->getContent());
        $this->assertLessThan(
            strpos($response->getContent(), 'Approved review 2'),
            strpos($response->getContent(), 'Approved review 1'),
        );
        $this->assertStringNotContainsString('Approved review 7', $response->getContent());
    }
}

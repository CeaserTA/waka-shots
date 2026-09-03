<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Services\DriveGalleryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

class PublicGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_gallery_renders_images_and_logs_view(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
            'expires_at' => now()->addDay(),
        ]);

        $drive = Mockery::mock(DriveGalleryService::class);
        $drive->shouldReceive('listImagesInFolder')->once()->with('folder_123')->andReturn([
            [
                'id' => 'image_123',
                'name' => 'portrait.jpg',
                'thumbnailLink' => 'https://drive.example/thumb.jpg',
                'webContentLink' => 'https://drive.example/full.jpg',
            ],
        ]);
        $this->app->instance(DriveGalleryService::class, $drive);

        $response = $this->withHeaders(['User-Agent' => 'Gallery Test Browser'])
            ->get(route('gallery.show', $gallery->access_token));

        $response->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('noindex, nofollow')
            ->assertSee('portrait.jpg');
        $this->assertDatabaseHas('gallery_access_logs', [
            'gallery_id' => $gallery->id,
            'event_type' => 'view',
            'user_agent' => 'Gallery Test Browser',
        ]);
    }

    public function test_inactive_or_expired_gallery_is_friendly_and_does_not_call_drive(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
            'expires_at' => now()->subMinute(),
            'is_active' => false,
        ]);

        $drive = Mockery::mock(DriveGalleryService::class);
        $drive->shouldNotReceive('listImagesInFolder');
        $this->app->instance(DriveGalleryService::class, $drive);

        $this->get(route('gallery.show', $gallery->access_token))
            ->assertOk()
            ->assertSee('This gallery is no longer available.');
    }

    public function test_image_download_is_streamed_by_the_app_and_logged(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ]);

        $drive = Mockery::mock(DriveGalleryService::class);
        // Serving one image no longer re-lists the whole folder to find it —
        // downloadFileInFolder() verifies folder membership from the file's
        // own metadata in the same call that fetches it.
        $drive->shouldNotReceive('listImagesInFolder');
        $drive->shouldReceive('downloadFileInFolder')->once()->with('image_123', 'folder_123')->andReturn([
            'contents' => 'image bytes',
            'name' => 'portrait.jpg',
            'mimeType' => 'image/jpeg',
        ]);
        $this->app->instance(DriveGalleryService::class, $drive);

        $response = $this->get(route('gallery.download', [$gallery->access_token, 'image_123']));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('Content-Disposition', 'attachment; filename="portrait.jpg"')
            ->assertContent('image bytes');
        $this->assertDatabaseHas('gallery_access_logs', [
            'gallery_id' => $gallery->id,
            'event_type' => 'download',
            'image_id' => 'image_123',
        ]);
    }

    public function test_download_all_route_has_its_own_rate_limit(): void
    {
        $route = Route::getRoutes()->getByName('gallery.download-all');

        $this->assertContains('throttle:gallery-download-all', $route->middleware());
        $this->assertContains('throttle:gallery-downloads', Route::getRoutes()->getByName('gallery.download')->middleware());
        $this->assertContains('throttle:gallery-downloads', Route::getRoutes()->getByName('gallery.preview')->middleware());
    }

    public function test_download_all_builds_a_zip_and_logs_one_access_event(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ]);

        $drive = Mockery::mock(DriveGalleryService::class);
        $drive->shouldReceive('listImagesInFolder')->once()->with('folder_123')->andReturn([
            ['id' => 'image_1', 'name' => 'one.jpg'],
            ['id' => 'image_2', 'name' => 'two.jpg'],
        ]);
        $drive->shouldReceive('downloadFile')->once()->with('image_1')->andReturn([
            'contents' => 'one', 'name' => 'one.jpg', 'mimeType' => 'image/jpeg',
        ]);
        $drive->shouldReceive('downloadFile')->once()->with('image_2')->andReturn([
            'contents' => 'two', 'name' => 'two.jpg', 'mimeType' => 'image/jpeg',
        ]);
        $this->app->instance(DriveGalleryService::class, $drive);

        $response = $this->get(route('gallery.download-all', $gallery->access_token));

        $response->assertOk()->assertHeader('Content-Type', 'application/zip');
        $this->assertDatabaseHas('gallery_access_logs', [
            'gallery_id' => $gallery->id,
            'event_type' => 'download_all',
        ]);
    }
}

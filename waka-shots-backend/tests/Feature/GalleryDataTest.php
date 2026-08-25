<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\Gallery;
use App\Models\GoogleDriveConnection;
use App\Models\User;
use App\Policies\GalleryPolicy;
use App\Services\DriveGalleryService;
use App\Support\DriveFolderUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_generates_a_secure_access_token(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ]);

        $this->assertSame(32, strlen($gallery->access_token));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $gallery->access_token);
        $this->assertNotSame($gallery->access_token, Gallery::create([
            'client_name' => 'John Doe',
            'event_name' => 'Birthday',
            'event_date' => '2026-09-02',
            'drive_folder_id' => 'folder_456',
        ])->access_token);
    }

    public function test_google_drive_tokens_use_encrypted_casts(): void
    {
        $connection = GoogleDriveConnection::create([
            'access_token' => 'access-secret',
            'refresh_token' => 'refresh-secret',
        ]);

        $this->assertSame('access-secret', $connection->fresh()->access_token);
        $this->assertStringNotContainsString('refresh-secret', (string) $connection->fresh()->getRawOriginal('refresh_token'));
    }

    public function test_drive_folder_links_are_normalized_to_ids(): void
    {
        $this->assertSame('folder_123', DriveFolderUrl::extractId('https://drive.google.com/drive/folders/folder_123?usp=sharing'));
        $this->assertSame('folder_456', DriveFolderUrl::extractId('https://drive.google.com/open?id=folder_456'));
    }

    public function test_drive_thumbnail_links_are_upscaled_without_changing_download_data(): void
    {
        $drive = app(DriveGalleryService::class);

        $this->assertSame(
            'https://lh3.googleusercontent.com/drive-storage/photo=s1600',
            $drive->enhanceThumbnailLink('https://lh3.googleusercontent.com/drive-storage/photo=s220'),
        );
        $this->assertSame(
            'https://lh3.googleusercontent.com/drive-storage/photo=s1600',
            $drive->enhanceThumbnailLink('https://lh3.googleusercontent.com/drive-storage/photo=s1000'),
        );
        $this->assertSame('https://example.com/photo', $drive->enhanceThumbnailLink('https://example.com/photo'));
        $this->assertNull($drive->enhanceThumbnailLink(null));
    }

    public function test_galleries_are_admin_only(): void
    {
        $policy = new GalleryPolicy();
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $editor = User::factory()->create(['role' => UserRole::Editor]);
        $gallery = Gallery::create([
            'client_name' => 'Client',
            'event_name' => 'Event',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ]);

        $this->assertTrue($policy->viewAny($admin));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertSame('Client Delivery', GalleryResource::getNavigationGroup());
        $this->assertTrue($policy->view($admin, $gallery));
        $this->assertFalse($policy->view($editor, $gallery));
    }
}

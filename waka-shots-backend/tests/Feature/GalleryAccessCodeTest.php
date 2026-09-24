<?php

namespace Tests\Feature;

use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Mail\GalleryAccessMail;
use App\Models\Gallery;
use App\Models\User;
use App\Services\DriveGalleryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class GalleryAccessCodeTest extends TestCase
{
    use RefreshDatabase;

    private function makeGallery(array $overrides = []): Gallery
    {
        return Gallery::create($overrides + [
            'client_name' => 'Jane Doe',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
            'expires_at' => now()->addDay(),
        ]);
    }

    private function mockDrive(): void
    {
        $drive = Mockery::mock(DriveGalleryService::class);
        $drive->shouldReceive('listImagesInFolder')->andReturn([]);
        $drive->shouldReceive('downloadFileInFolder')->andReturn(['contents' => 'x', 'mimeType' => 'image/jpeg', 'name' => 'a.jpg']);
        $drive->shouldReceive('fetchThumbnail')->andReturn(['contents' => 'x', 'mimeType' => 'image/jpeg']);
        $this->app->instance(DriveGalleryService::class, $drive);
    }

    public function test_access_code_is_hashed_and_verifiable(): void
    {
        $gallery = $this->makeGallery();
        $this->assertFalse($gallery->hasPassword());
        $this->assertFalse($gallery->verifyPassword('123456'));

        $code = $gallery->setNewAccessCode();
        $gallery->save();

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        $this->assertNotSame($code, $gallery->fresh()->getRawOriginal('password_hash'));
        $this->assertTrue($gallery->fresh()->verifyPassword($code));
        $this->assertFalse($gallery->fresh()->verifyPassword('000000'));
    }

    public function test_legacy_gallery_without_code_stays_open(): void
    {
        $this->mockDrive();
        $gallery = $this->makeGallery();

        $this->get(route('gallery.show', $gallery->access_token))
            ->assertOk()
            ->assertDontSee('Unlock gallery');
    }

    public function test_every_content_route_is_locked_until_unlocked(): void
    {
        $this->mockDrive();
        $gallery = $this->makeGallery();
        $code = $gallery->setNewAccessCode();
        $gallery->save();
        $token = $gallery->access_token;

        $urls = [
            route('gallery.show', $token),
            route('gallery.preview', [$token, 'img']),
            route('gallery.download', [$token, 'img']),
            route('gallery.thumb', [$token, 'img']),
            route('gallery.download-all', $token),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertForbidden()->assertSee('Unlock gallery');
        }

        $this->post(route('gallery.testimonial', $token), ['rating' => 5, 'quote' => 'Lovely photos, thank you!'])
            ->assertForbidden();
        $this->assertDatabaseCount('testimonials', 0);

        $this->post(route('gallery.unlock', $token), ['code' => $code])
            ->assertRedirect(route('gallery.show', $token));

        $this->get(route('gallery.show', $token))->assertOk()->assertDontSee('Unlock gallery');
        $this->get(route('gallery.preview', [$token, 'img']))->assertOk();
        $this->get(route('gallery.thumb', [$token, 'img']))->assertOk();
    }

    public function test_wrong_code_fails_and_is_logged(): void
    {
        $gallery = $this->makeGallery();
        $gallery->setNewAccessCode();
        $gallery->save();

        $this->from(route('gallery.show', $gallery->access_token))
            ->post(route('gallery.unlock', $gallery->access_token), ['code' => 'nope'])
            ->assertRedirect(route('gallery.show', $gallery->access_token))
            ->assertSessionHasErrors('code');

        $this->assertDatabaseHas('gallery_access_logs', [
            'gallery_id' => $gallery->id,
            'event_type' => 'password_failed',
        ]);
    }

    public function test_unlock_route_is_rate_limited(): void
    {
        $this->assertContains('throttle:gallery-unlock', Route::getRoutes()->getByName('gallery.unlock')->middleware());
    }

    public function test_rotating_the_code_invalidates_old_code_and_unlocked_sessions(): void
    {
        $this->mockDrive();
        $gallery = $this->makeGallery();
        $old = $gallery->setNewAccessCode();
        $gallery->save();

        $this->post(route('gallery.unlock', $gallery->access_token), ['code' => $old]);
        $this->get(route('gallery.show', $gallery->access_token))->assertOk();

        $new = $gallery->setNewAccessCode();
        $gallery->save();

        $this->get(route('gallery.show', $gallery->access_token))->assertForbidden();
        $this->assertFalse($gallery->fresh()->verifyPassword($old));
        $this->assertTrue($gallery->fresh()->verifyPassword($new));
    }

    private function createFormData(array $overrides = []): array
    {
        return $overrides + [
            'client_name' => 'Jane Doe',
            'client_email' => 'jane@example.test',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_link' => 'https://drive.google.com/drive/folders/folder_abc123',
            'expires_at' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ];
    }

    public function test_creating_a_gallery_in_filament_issues_a_code_and_emails_it(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateGallery::class)
            ->fillForm($this->createFormData())
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $gallery = Gallery::sole();
        $this->assertTrue($gallery->hasPassword());
        Mail::assertQueued(GalleryAccessMail::class, fn (GalleryAccessMail $mail) => $mail->hasTo('jane@example.test')
            && $gallery->verifyPassword($mail->accessCode));
    }

    public function test_client_email_is_required_on_create(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(CreateGallery::class)
            ->fillForm($this->createFormData(['client_email' => null]))
            ->call('create')
            ->assertHasFormErrors(['client_email' => 'required']);

        $this->assertDatabaseCount('galleries', 0);
    }

    public function test_legacy_gallery_can_be_edited_without_adding_an_email(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $gallery = $this->makeGallery();

        Livewire::test(EditGallery::class, ['record' => $gallery->getRouteKey()])
            ->fillForm(['expires_at' => now()->addMonths(2)->toDateString()])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($gallery->fresh()->client_email);
    }

    public function test_resend_without_email_shows_code_but_sends_nothing(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $gallery = $this->makeGallery();

        Livewire::test(ListGalleries::class)
            ->callTableAction('resendAccessEmail', $gallery)
            ->assertNotified();

        $this->assertTrue($gallery->fresh()->hasPassword());
        Mail::assertNothingQueued();
    }

    public function test_resend_with_email_queues_the_new_code(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $gallery = $this->makeGallery(['client_email' => 'jane@example.test']);

        Livewire::test(ListGalleries::class)->callTableAction('resendAccessEmail', $gallery);

        Mail::assertQueued(GalleryAccessMail::class, fn (GalleryAccessMail $mail) => $mail->hasTo('jane@example.test')
            && $gallery->fresh()->verifyPassword($mail->accessCode));
    }

    public function test_resend_action_rotates_the_code(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $gallery = $this->makeGallery();

        Livewire::test(ListGalleries::class)
            ->callTableAction('resendAccessEmail', $gallery)
            ->assertNotified();
        $first = $gallery->fresh()->getRawOriginal('password_hash');
        $this->assertNotNull($first);

        Livewire::test(ListGalleries::class)->callTableAction('resendAccessEmail', $gallery);
        $this->assertNotSame($first, $gallery->fresh()->getRawOriginal('password_hash'));
    }

    public function test_access_mail_renders_link_and_code(): void
    {
        $gallery = $this->makeGallery();

        $html = (new GalleryAccessMail($gallery, '482913'))->render();

        $this->assertStringContainsString('482913', $html);
        $this->assertStringContainsString(route('gallery.show', $gallery->access_token), $html);
        $this->assertStringContainsString('Jane Doe', $html);
        $this->assertSame('Jane Doe, your Wedding photos are ready', (new GalleryAccessMail($gallery, '1'))->envelope()->subject);
    }
}

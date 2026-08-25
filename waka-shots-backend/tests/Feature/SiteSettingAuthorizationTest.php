<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use App\Models\User;
use App\Policies\SiteSettingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_setting_can_be_created_with_mass_assignment(): void
    {
        $setting = SiteSetting::create([
            'studio_name' => 'Waka Shots',
            'contact_email' => 'hello@example.com',
            'hero_tagline' => 'Stories worth remembering.',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'id' => $setting->id,
            'studio_name' => 'Waka Shots',
            'contact_email' => 'hello@example.com',
        ]);
    }

    public function test_only_admins_are_authorized_for_site_settings(): void
    {
        $policy = new SiteSettingPolicy();
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $editor = User::factory()->create(['role' => UserRole::Editor]);
        $setting = SiteSetting::create(['studio_name' => 'Waka Shots']);

        foreach (['viewAny', 'view', 'create', 'update', 'delete'] as $ability) {
            $arguments = $ability === 'viewAny' || $ability === 'create'
                ? [$admin]
                : [$admin, $setting];
            $editorArguments = $ability === 'viewAny' || $ability === 'create'
                ? [$editor]
                : [$editor, $setting];

            $this->assertTrue($policy->{$ability}(...$arguments), $ability . ' should allow admins');
            $this->assertFalse($policy->{$ability}(...$editorArguments), $ability . ' should deny editors');
        }
    }

    public function test_site_setting_resource_metadata_is_configured(): void
    {
        $this->assertSame('Settings', SiteSettingResource::getNavigationGroup());
        $this->assertSame('Studio Settings', SiteSettingResource::getNavigationLabel());
        $this->assertSame(SiteSetting::class, SiteSettingResource::getModel());
    }
}

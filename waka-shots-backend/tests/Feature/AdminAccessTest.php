<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Panel;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    public function test_panel_access_restricts_to_admin_and_editor_roles(): void
    {
        $panel = Panel::make()->id('admin');

        $this->assertTrue((new User(['role' => UserRole::Admin]))->canAccessPanel($panel));
        $this->assertTrue((new User(['role' => UserRole::Editor]))->canAccessPanel($panel));
        $this->assertTrue((new User(['role' => 'admin']))->canAccessPanel($panel));
        $this->assertTrue((new User(['role' => 'editor']))->canAccessPanel($panel));
        $this->assertFalse((new User(['role' => null]))->canAccessPanel($panel));
    }

    public function test_unauthenticated_users_are_redirected_from_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}

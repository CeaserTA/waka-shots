<?php

namespace Tests\Feature;

use Tests\TestCase;

class GoogleDriveAuthRoutingTest extends TestCase
{
    public function test_unauthenticated_google_oauth_requests_redirect_to_filament_login(): void
    {
        $response = $this->get('/auth/google/callback?code=test-code');

        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_session_uses_lax_same_site_cookies_for_oauth_return(): void
    {
        $this->assertSame('lax', config('session.same_site'));
    }
}

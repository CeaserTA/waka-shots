<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_an_enquiry_can_be_submitted(): void
    {
        $service = Service::create([
            'name' => 'Wedding Photography',
            'has_packages' => true,
        ]);

        $response = $this->post(route('enquiries.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+256700000000',
            'service_id' => $service->id,
            'preferred_date' => '2026-09-12',
            'location' => 'Kampala',
            'details' => 'Wedding coverage for our ceremony.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('enquiries', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+256700000000',
            'service_id' => $service->id,
            'package_id' => null,
            'preferred_date' => '2026-09-12',
            'location' => 'Kampala',
            // Budget is no longer collected, and status is assigned by the
            // controller rather than accepted from the request.
            'budget' => null,
            'details' => 'Wedding coverage for our ceremony.',
            'status' => 'new',
        ]);
        $this->assertSame(1, Enquiry::count());
    }

    public function test_logged_out_users_are_redirected_to_filament_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}

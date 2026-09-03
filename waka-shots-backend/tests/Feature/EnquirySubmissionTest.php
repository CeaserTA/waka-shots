<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquirySubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_an_enquiry_redirects_to_contact_with_a_success_message(): void
    {
        $response = $this->post(route('enquiries.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'details' => 'Interested in a wedding shoot.',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');
    }

    public function test_the_success_message_renders_as_a_toast_on_the_contact_page(): void
    {
        $this->post(route('enquiries.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'details' => 'Interested in a wedding shoot.',
        ]);

        $this->followingRedirects()
            ->get(route('contact'))
            ->assertOk();
    }

    public function test_an_enquiry_is_stored_as_new_and_cannot_have_its_status_spoofed(): void
    {
        $this->post(route('enquiries.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'details' => 'Interested in a wedding shoot.',
            'status' => 'booked',
            'budget' => 'UGX 6,000,000+',
        ]);

        $enquiry = Enquiry::firstWhere('email', 'jane@example.com');

        $this->assertNotNull($enquiry);
        $this->assertSame('new', $enquiry->status);
        $this->assertNull($enquiry->budget);
    }
}

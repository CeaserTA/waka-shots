<?php

namespace Tests\Feature;

use App\Mail\NewEnquiryMail;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_an_enquiry_is_emailed_to_the_studio_inbox(): void
    {
        Mail::fake();
        config(['mail.enquiry_recipient' => 'hello@wakashots.com']);

        $this->post(route('enquiries.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'details' => 'Interested in a wedding shoot.',
        ]);

        $this->assertDatabaseHas('enquiries', ['email' => 'jane@example.com']);

        Mail::assertQueued(NewEnquiryMail::class, function (NewEnquiryMail $mail) {
            return $mail->hasTo('hello@wakashots.com')
                && $mail->hasReplyTo('jane@example.com')
                && $mail->enquiry->email === 'jane@example.com';
        });
    }

    public function test_the_enquiry_email_renders_the_submitted_details(): void
    {
        $enquiry = Enquiry::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+256700000000',
            'details' => 'Interested in a wedding shoot.',
            'status' => 'new',
        ]);

        (new NewEnquiryMail($enquiry))
            ->assertSeeInHtml('Jane Doe')
            ->assertSeeInHtml('+256700000000')
            ->assertSeeInHtml('Interested in a wedding shoot.');
    }

    public function test_no_enquiry_email_is_sent_when_the_recipient_is_blank(): void
    {
        Mail::fake();
        config(['mail.enquiry_recipient' => null]);

        $this->post(route('enquiries.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'details' => 'Interested in a wedding shoot.',
        ]);

        Mail::assertNothingQueued();
        $this->assertDatabaseHas('enquiries', ['email' => 'jane@example.com']);
    }
}

<?php

namespace App\Mail;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Enquiry $enquiry,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Reply-To the client so hitting "Reply" in the inbox answers
            // them directly instead of the studio's own address.
            replyTo: [new Address($this->enquiry->email, $this->enquiry->name)],
            subject: 'New enquiry from ' . $this->enquiry->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-enquiry',
            with: [
                'dashboardUrl' => EnquiryResource::getUrl('view', ['record' => $this->enquiry]),
            ],
        );
    }
}

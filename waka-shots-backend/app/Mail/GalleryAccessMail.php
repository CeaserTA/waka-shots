<?php

namespace App\Mail;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GalleryAccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Gallery $gallery,
        public string $accessCode,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->gallery->client_name}, your {$this->gallery->event_name} photos are ready",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gallery-access',
            with: [
                'galleryUrl' => route('gallery.show', $this->gallery->access_token),
            ],
        );
    }
}

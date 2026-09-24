<?php

namespace App\Support;

use App\Mail\GalleryAccessMail;
use App\Models\Gallery;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class GalleryAccessCode
{
    /**
     * Rotates the gallery's access code (invalidating any previous one),
     * emails it to the client when an address is on file, and shows the
     * admin the plaintext code once.
     */
    public static function issue(Gallery $gallery): string
    {
        $code = $gallery->setNewAccessCode();
        $gallery->save();

        $recipient = static::recipientFor($gallery);

        if ($recipient) {
            Mail::to($recipient)->queue(new GalleryAccessMail($gallery, $code));
        }

        Notification::make()
            ->title('Access code: ' . $code)
            ->body(($recipient
                ? 'Emailed to ' . $recipient . '. '
                : 'No email was sent: there is no client email on file. Share this code manually, or add their email on the Edit page first. ')
                . 'This code is shown only once and cannot be retrieved later.')
            ->icon('heroicon-o-key')
            ->warning()
            ->persistent()
            ->send();

        return $code;
    }

    /**
     * Null for legacy galleries created before client emails were collected.
     */
    private static function recipientFor(Gallery $gallery): ?string
    {
        return $gallery->client_email;
    }
}

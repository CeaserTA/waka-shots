<?php

namespace App\Support;

class WhatsApp
{
    /**
     * Build a wa.me click-to-chat link for any phone number, or null when the
     * number has no digits so callers can skip rendering.
     *
     * This is the only place wa.me URLs are built — the studio's own number
     * (SiteSetting::whatsappLink) and client numbers (Enquiries table) both
     * go through here. wa.me needs digits only: no "+", spaces or dashes.
     */
    public static function link(?string $number, ?string $message = null): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $number);

        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/' . $digits . (filled($message) ? '?text=' . rawurlencode($message) : '');
    }
}

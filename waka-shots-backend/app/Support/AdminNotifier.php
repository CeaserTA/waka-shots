<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminNotifier
{
    /**
     * Deliver a notification to every panel user.
     *
     * Notifications are a side effect of things like a client submitting an
     * enquiry — a failure here (no users yet, notifications table missing on
     * a half-migrated environment) must never take down the client-facing
     * action that triggered it, so everything is best-effort and logged.
     */
    public static function send(Notification $notification): void
    {
        try {
            $recipients = User::query()
                ->whereIn('role', [UserRole::Admin->value, UserRole::Editor->value])
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            // notifyNow(), not Filament's sendToDatabase(): its
            // DatabaseNotification implements ShouldQueue, so on this app's
            // database queue driver every notification sat unprocessed in the
            // jobs table until a worker happened to be running — which meant
            // no bell, ever. Writing one row is cheap enough that deferring it
            // buys nothing but a dependency on a live worker.
            $databaseNotification = $notification->toDatabase();

            foreach ($recipients as $recipient) {
                $recipient->notifyNow($databaseNotification);
            }

            // Filament's DatabaseNotificationsSent event is deliberately not
            // dispatched here: it is queued/broadcast, so without a worker it
            // just accumulates dead rows in the jobs table. The panel's
            // notification polling picks new records up on its own.
        } catch (Throwable $exception) {
            Log::warning('Unable to send admin notification.', [
                'title' => $notification->getTitle(),
                'exception' => $exception,
            ]);
        }
    }
}

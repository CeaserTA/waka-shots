<?php

namespace App\Observers;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Mail\NewEnquiryMail;
use App\Models\Enquiry;
use App\Support\AdminNotifier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnquiryObserver
{
    public function created(Enquiry $enquiry): void
    {
        AdminNotifier::send(
            Notification::make()
                ->title('New enquiry received')
                ->body(trim($enquiry->name . ' · ' . ($enquiry->service?->name ?? 'No service selected')))
                ->icon('heroicon-o-inbox-arrow-down')
                ->success()
                ->actions([
                    Action::make('view')
                        ->label('View enquiry')
                        ->url(EnquiryResource::getUrl('view', ['record' => $enquiry]))
                        ->markAsRead(),
                ])
        );

        $this->emailStudio($enquiry);
    }

    /**
     * Copy the enquiry to the studio inbox as well as the dashboard.
     *
     * Like the dashboard notification this is best-effort: the enquiry is
     * already saved, so a mail/queue failure must not turn the client's
     * submission into an error page.
     */
    private function emailStudio(Enquiry $enquiry): void
    {
        $recipient = config('mail.enquiry_recipient');

        if (blank($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->queue(new NewEnquiryMail($enquiry));
        } catch (Throwable $exception) {
            Log::warning('Unable to email new enquiry.', [
                'enquiry_id' => $enquiry->id,
                'exception' => $exception,
            ]);
        }
    }
}

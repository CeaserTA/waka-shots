<?php

namespace App\Observers;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use App\Support\AdminNotifier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

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
    }
}

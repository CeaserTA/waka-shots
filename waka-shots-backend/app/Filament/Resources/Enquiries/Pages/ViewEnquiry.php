<?php

namespace App\Filament\Resources\Enquiries\Pages;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEnquiry extends ViewRecord
{
    protected static string $resource = EnquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Update Status')
                ->color('primary')
                ->modalHeading('Update Enquiry Status')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Save Changes')
                ->fillForm(fn (Enquiry $record): array => [
                    'status' => $record->status,
                ])
                ->form([
                    Select::make('status')
                        ->options([
                            'new' => 'New',
                            'contacted' => 'Contacted',
                            'booked' => 'Booked',
                            'closed' => 'Closed',
                        ])
                        ->native(false)
                        ->required(),
                ])
                ->action(function (Enquiry $record, array $data): void {
                    $record->update(['status' => $data['status']]);

                    Notification::make()
                        ->title('Enquiry status updated')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}

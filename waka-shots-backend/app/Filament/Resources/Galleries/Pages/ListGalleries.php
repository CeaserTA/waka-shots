<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\GoogleDriveConnection as GoogleDriveConnectionModel;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('googleDrive')
                ->label('Google Drive')
                ->icon('heroicon-o-cloud')
                ->modalHeading('Google Drive Settings')
                ->modalWidth('md')
                ->modalContent(fn () => view('filament.modals.google-drive-settings', [
                    'connection' => GoogleDriveConnectionModel::query()->latest('id')->first(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalFooterActions(function (): array {
                    $isConnected = GoogleDriveConnectionModel::query()->whereNotNull('refresh_token')->exists();

                    return [
                        Action::make('connect')
                            ->label('Connect Google Drive')
                            ->url(route('google.redirect'))
                            ->visible(! $isConnected),
                        Action::make('disconnect')
                            ->label('Disconnect')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->visible($isConnected)
                            ->action(function (): void {
                                GoogleDriveConnectionModel::query()->delete();
                                Notification::make()
                                    ->title('Google Drive disconnected')
                                    ->success()
                                    ->send();
                            }),
                    ];
                }),
        ];
    }

    public function getSubheading(): ?string
    {
        return GoogleDriveConnectionModel::query()->whereNotNull('refresh_token')->exists()
            ? null
            : 'Google Drive is not connected. Use the Connect Google Drive button to authorize access before syncing galleries.';
    }
}

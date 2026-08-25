<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\GoogleDriveConnection as GoogleDriveConnectionModel;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GoogleDriveConnection extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = \Filament\Support\Icons\Heroicon::OutlinedCloud;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Google Drive';

    protected static ?string $slug = 'google-drive';

    protected string $view = 'filament.pages.google-drive-connection';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === UserRole::Admin || auth()->user()?->role === UserRole::Admin->value;
    }

    public function getConnection(): ?GoogleDriveConnectionModel
    {
        return GoogleDriveConnectionModel::query()->latest('id')->first();
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('connect')
                ->label('Connect Google Drive')
                ->url(route('google.redirect'))
                ->visible(fn (): bool => $this->getConnection() === null),
            Action::make('disconnect')
                ->label('Disconnect')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->getConnection() !== null)
                ->action(function (): void {
                    GoogleDriveConnectionModel::query()->delete();
                    Notification::make()->title('Google Drive disconnected')->success()->send();
                }),
        ];
    }
}

<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Pages\GoogleDriveConnection;
use App\Models\GoogleDriveConnection as GoogleDriveConnectionModel;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('connectDrive')
                ->label('Connect Google Drive')
                ->url(GoogleDriveConnection::getUrl())
                ->visible(fn (): bool => ! GoogleDriveConnectionModel::query()->whereNotNull('refresh_token')->exists()),
        ];
    }

    public function getSubheading(): ?string
    {
        return GoogleDriveConnectionModel::query()->whereNotNull('refresh_token')->exists()
            ? null
            : 'Google Drive is not connected. Use the Connect Google Drive button to authorize access before syncing galleries.';
    }
}

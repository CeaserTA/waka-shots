<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Support\DriveFolderUrl;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGallery extends EditRecord
{
    protected static string $resource = GalleryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['drive_folder_id'] = DriveFolderUrl::extractId($data['drive_folder_link']);
        unset($data['drive_folder_link']);
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

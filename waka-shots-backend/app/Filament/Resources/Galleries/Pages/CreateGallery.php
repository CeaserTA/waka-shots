<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Support\DriveFolderUrl;
use Filament\Resources\Pages\CreateRecord;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['drive_folder_id'] = DriveFolderUrl::extractId($data['drive_folder_link']);
        unset($data['drive_folder_link']);
        return $data;
    }
}

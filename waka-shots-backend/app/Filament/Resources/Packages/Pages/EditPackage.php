<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Schemas\PackageForm;
use Filament\Resources\Pages\EditRecord;

class EditPackage extends EditRecord
{
    protected static string $resource = PackageResource::class;

    protected ?string $featuresText = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->featuresText = $data['features_text'] ?? null;

        return $data;
    }

    protected function afterSave(): void
    {
        PackageForm::syncFeatures($this->record, $this->featuresText);
    }
}

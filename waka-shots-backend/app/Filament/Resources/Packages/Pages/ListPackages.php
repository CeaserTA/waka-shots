<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Schemas\PackageForm;
use App\Models\Package;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackages extends ListRecords
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('New Package')
                ->modalHeading('New Package')
                ->modalWidth('xl')
                ->modalSubmitActionLabel('Create Package')
                ->after(function (Package $record, array $data): void {
                    PackageForm::syncFeatures($record, $data['features_text'] ?? null);
                }),
        ];
    }
}

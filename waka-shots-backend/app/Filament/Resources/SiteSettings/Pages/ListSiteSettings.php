<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    public function mount(): void
    {
        $record = SiteSetting::query()->first();

        $this->redirect(
            $record
                ? SiteSettingResource::getUrl('edit', ['record' => $record])
                : SiteSettingResource::getUrl('create')
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

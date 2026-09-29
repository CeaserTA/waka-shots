<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    /**
     * List fields (stats, values, process steps, hero words and tags) can't
     * show their defaults as a placeholder, so blank ones are pre-filled with
     * the lists the site is currently rendering.
     */
    protected const LIST_KEYS = [
        'stats',
        'home.hero_words',
        'home.hero_tags',
        'about.values',
        'services.process',
    ];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (self::LIST_KEYS as $key) {
            if (blank(data_get($data, "content.{$key}"))) {
                data_set($data, "content.{$key}", SiteSetting::contentDefault($key));
            }
        }

        return $data;
    }
}

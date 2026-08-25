<?php

namespace App\Filament\Resources\JournalPosts\Pages;

use App\Filament\Resources\JournalPosts\JournalPostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJournalPosts extends ListRecords
{
    protected static string $resource = JournalPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

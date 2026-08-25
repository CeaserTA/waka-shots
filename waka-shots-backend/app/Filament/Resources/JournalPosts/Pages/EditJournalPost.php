<?php

namespace App\Filament\Resources\JournalPosts\Pages;

use App\Filament\Resources\JournalPosts\JournalPostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJournalPost extends EditRecord
{
    protected static string $resource = JournalPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

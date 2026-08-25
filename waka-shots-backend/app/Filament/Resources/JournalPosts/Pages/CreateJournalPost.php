<?php

namespace App\Filament\Resources\JournalPosts\Pages;

use App\Filament\Resources\JournalPosts\JournalPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalPost extends CreateRecord
{
    protected static string $resource = JournalPostResource::class;
}

<?php

namespace App\Filament\Resources\Films\Pages;

use App\Filament\Resources\Films\FilmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFilms extends ManageRecords
{
    protected static string $resource = FilmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('New Film')
                ->modalHeading('New Film')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Create Film'),
        ];
    }
}

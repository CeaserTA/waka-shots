<?php

namespace App\Filament\Resources\Films\Pages;

use App\Filament\Resources\Films\FilmResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFilm extends CreateRecord
{
    protected static string $resource = FilmResource::class;
}

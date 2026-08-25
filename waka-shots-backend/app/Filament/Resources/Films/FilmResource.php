<?php

namespace App\Filament\Resources\Films;

use App\Filament\Resources\Films\Pages\CreateFilm;
use App\Filament\Resources\Films\Pages\EditFilm;
use App\Filament\Resources\Films\Pages\ListFilms;
use App\Filament\Resources\Films\Schemas\FilmForm;
use App\Filament\Resources\Films\Tables\FilmsTable;
use App\Models\Film;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FilmResource extends Resource
{
    protected static ?string $model = Film::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Films';

    public static function form(Schema $schema): Schema
    {
        return FilmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FilmsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFilms::route('/'),
            'create' => CreateFilm::route('/create'),
            'edit' => EditFilm::route('/{record}/edit'),
        ];
    }
}

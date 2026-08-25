<?php

namespace App\Filament\Resources\Films\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FilmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('youtube_id')
                    ->label('YouTube Video ID')
                    ->required()
                    ->maxLength(20)
                    ->helperText('Enter the 11-character video ID, e.g., dQw4w9WgXcQ'),
            ]);
    }
}

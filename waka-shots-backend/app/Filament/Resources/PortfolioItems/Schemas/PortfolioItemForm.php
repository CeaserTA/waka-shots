<?php

namespace App\Filament\Resources\PortfolioItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PortfolioItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('image_path')
                    ->label('Image path')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('/images/portfolio/example.jpg'),
            ]);
    }
}

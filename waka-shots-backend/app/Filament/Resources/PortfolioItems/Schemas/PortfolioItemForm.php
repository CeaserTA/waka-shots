<?php

namespace App\Filament\Resources\PortfolioItems\Schemas;

use Filament\Forms\Components\FileUpload;
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
                FileUpload::make('image_path')
                    ->label('Portfolio Image')
                    ->disk('r2')
                    ->directory('portfolio-images')
                    ->acceptedFileTypes(['image/*'])
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->maxSize(10240),
            ]);
    }
}

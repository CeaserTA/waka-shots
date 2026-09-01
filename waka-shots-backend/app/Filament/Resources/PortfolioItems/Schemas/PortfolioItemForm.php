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
                    ->maxLength(255),
                FileUpload::make('image_path')
                    ->label('Portfolio Image')
                    ->disk('r2')
                    ->visibility('public')
                    ->fetchFileInformation(false)
                    ->directory('portfolio-images')
                    ->acceptedFileTypes(['image/*'])
                    ->image()
                    ->imageEditor()
                    ->automaticallyResizeImagesToWidth(2500)
                    ->automaticallyResizeImagesMode('contain')
                    ->imageResizeUpscale(false)
                    ->required()
                    ->maxSize(10240),
            ]);
    }
}

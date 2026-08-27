<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Service Name')
                    ->placeholder('e.g., Weddings, Commercial, Portraits')
                    ->required()
                    ->maxLength(255),
                TextInput::make('tagline')
                    ->label('Tagline')
                    ->placeholder('e.g., f/1.8 — Intimate')
                    ->maxLength(100)
                    ->helperText('Short label shown on the homepage service card (e.g. "f/1.8 — Intimate"). Optional — falls back to a generic label if left blank.'),
                Textarea::make('description')
                    ->label('Service Description')
                    ->rows(4)
                    ->maxLength(2000)
                    ->columnSpanFull(),
                FileUpload::make('thumbnail_path')
                    ->label('Service Thumbnail')
                    ->disk('r2')
                    ->fetchFileInformation(false)
                    ->directory('service-thumbnails')
                    ->acceptedFileTypes(['image/*'])
                    ->image()
                    ->imageEditor()
                    ->automaticallyResizeImagesToWidth(1600)
                    ->automaticallyResizeImagesMode('contain')
                    ->imageResizeUpscale(false),
                Toggle::make('has_packages')
                    ->label('Offers Packages')
                    ->default(true)
                    ->live(),
                TextInput::make('amount')
                    ->label('Service Amount')
                    ->numeric()
                    ->prefix('UGX')
                    ->visible(fn (Get $get): bool => ! $get('has_packages'))
                    ->required(fn (Get $get): bool => ! $get('has_packages'))
                    ->minValue(0)
                    ->maxValue(999999999999.99),
                TextInput::make('sort_order')
                    ->label('Display Order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Controls the order this service appears on the homepage — lower numbers show first.'),
            ]);
    }
}

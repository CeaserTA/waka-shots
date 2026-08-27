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
                Textarea::make('description')
                    ->label('Service Description')
                    ->rows(4)
                    ->maxLength(2000)
                    ->columnSpanFull(),
                FileUpload::make('thumbnail_path')
                    ->label('Service Thumbnail')
                    ->disk('r2')
                    ->directory('service-thumbnails')
                    ->acceptedFileTypes(['image/*'])
                    ->image()
                    ->imageEditor(),
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
            ]);
    }
}

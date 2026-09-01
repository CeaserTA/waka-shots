<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Partner Name')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo_path')
                    ->label('Partner Logo')
                    ->disk('r2')
                    ->fetchFileInformation(false)
                    ->directory('partner-logos')
                    ->acceptedFileTypes(['image/*'])
                    ->image()
                    ->imageEditor()
                    ->automaticallyResizeImagesToWidth(1200)
                    ->automaticallyResizeImagesMode('contain')
                    ->imageResizeUpscale(false)
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('website_url')
                    ->label('Website URL')
                    ->url()
                    ->placeholder('https://...'),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}

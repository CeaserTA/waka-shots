<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Studio Profile')
                    ->schema([
                        TextInput::make('studio_name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('hero_tagline')
                            ->label('Hero Tagline')
                            ->columnSpanFull(),
                    ]),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_email')
                            ->email()
                            ->label('Contact Email'),
                        TextInput::make('contact_phone')
                            ->label('Contact Phone'),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number'),
                        Textarea::make('address')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Section::make('Social Media Links')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->url()
                            ->label('Instagram URL'),
                        TextInput::make('youtube_url')
                            ->url()
                            ->label('YouTube URL'),
                        TextInput::make('facebook_url')
                            ->url()
                            ->label('Facebook URL'),
                    ])
                    ->columns(3),
            ]);
    }
}

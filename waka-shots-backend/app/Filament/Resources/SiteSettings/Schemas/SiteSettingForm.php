<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
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
                            ->label('Homepage Hero Tagline')
                            ->helperText('Replaces the "Stories, beautifully captured." headline on the homepage. Leave blank to keep the default.')
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
                        TextInput::make('tiktok_url')
                            ->url()
                            ->label('TikTok URL'),
                    ])
                    ->columns(4),
                Section::make('Homepage')
                    ->description('The hero banner and "Trusted By" partners band.')
                    ->schema([
                        FileUpload::make('home_hero_image')
                            ->label('Hero Background Image')
                            ->disk('r2')
                            ->directory('site-settings')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->helperText('Full-bleed image behind the homepage headline. Leave blank to keep the default.'),
                        FileUpload::make('home_partners_image')
                            ->label('Partners Band Background Image')
                            ->disk('r2')
                            ->directory('site-settings')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->helperText('Background behind the "Trusted By / Our Partners" band. Leave blank to keep the default.'),
                    ])
                    ->columns(2),
                Section::make('Portfolio Page')
                    ->schema([
                        FileUpload::make('portfolio_hero_image')
                            ->label('Hero Background Image')
                            ->disk('r2')
                            ->directory('site-settings')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('Leave blank to keep the default.'),
                        TextInput::make('portfolio_hero_eyebrow')
                            ->label('Eyebrow Label')
                            ->maxLength(100)
                            ->placeholder('Our Work'),
                        TextInput::make('portfolio_hero_heading')
                            ->label('Heading')
                            ->maxLength(100)
                            ->placeholder('Portfolio'),
                    ])
                    ->columns(2),
                Section::make('Contact Page')
                    ->schema([
                        FileUpload::make('contact_image')
                            ->label('Studio Image')
                            ->disk('r2')
                            ->directory('site-settings')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('Shown beside the contact form. Leave blank to keep the default.'),
                        TextInput::make('contact_tagline')
                            ->label('Heading')
                            ->maxLength(150)
                            ->placeholder("Let's create something unforgettable."),
                    ]),
                Section::make('Our Story')
                    ->description('The "Who We Are" story section on the About page.')
                    ->schema([
                        FileUpload::make('story_image')
                            ->label('Story Image')
                            ->disk('r2')
                            ->directory('site-settings')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('Leave blank to keep the default.'),
                        TextInput::make('story_heading')
                            ->label('Heading')
                            ->maxLength(150)
                            ->placeholder('More than photographs. Moments with meaning.')
                            ->columnSpanFull(),
                        Textarea::make('story_text')
                            ->label('Story')
                            ->rows(6)
                            ->helperText('Separate paragraphs with a blank line. Leave blank to keep the default.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Meet the Photographer')
                    ->description('The bio section on the About page.')
                    ->schema([
                        FileUpload::make('photographer_image')
                            ->label('Photographer Image')
                            ->disk('r2')
                            ->directory('site-settings')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('Leave blank to keep the default.'),
                        TextInput::make('photographer_heading')
                            ->label('Heading')
                            ->maxLength(150)
                            ->placeholder('Behind every frame.')
                            ->columnSpanFull(),
                        Textarea::make('photographer_bio')
                            ->label('Bio')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Section::make('Footer')
                    ->schema([
                        Textarea::make('footer_about_text')
                            ->label('About Blurb')
                            ->helperText('Short description under the studio name in the footer. Leave blank to keep the default.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

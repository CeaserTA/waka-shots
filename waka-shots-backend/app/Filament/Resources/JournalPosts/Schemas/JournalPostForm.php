<?php

namespace App\Filament\Resources\JournalPosts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class JournalPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Details')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, callable $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('URL slug')
                            ->prefix('/journal/')
                            ->required(fn (string $operation): bool => $operation === 'edit')
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Filled in from the title. Changing it on a published post breaks existing links to it.'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),
                    ])
                    ->columns(2),
                Section::make('Thumbnail')
                    ->description('Shown on the Journal page, at the top of the post, and when the post is shared on social media.')
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->label('Thumbnail Image')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->disk('r2')
                            ->visibility('public')
                            ->fetchFileInformation(false)
                            ->directory('journal-thumbnails')
                            ->acceptedFileTypes(['image/*'])
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['4:3', '16:9', null])
                            ->automaticallyResizeImagesToWidth(1800)
                            ->automaticallyResizeImagesMode('contain')
                            ->imageResizeUpscale(false)
                            ->helperText('A landscape photo works best. It is cropped to 4:3 on the Journal page.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Post Body')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

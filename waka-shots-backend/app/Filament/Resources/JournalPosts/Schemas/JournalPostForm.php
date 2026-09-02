<?php

namespace App\Filament\Resources\JournalPosts\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->maxLength(255),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),
                    ])
                    ->columns(3),
                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Post Body')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

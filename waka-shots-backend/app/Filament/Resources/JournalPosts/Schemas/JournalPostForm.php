<?php

namespace App\Filament\Resources\JournalPosts\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JournalPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Checkbox::make('is_published')
                    ->label('Published')
                    ->default(false),
                RichEditor::make('content')
                    ->label('Post Body')
                    ->columnSpanFull(),
            ]);
    }
}

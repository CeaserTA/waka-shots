<?php

namespace App\Filament\Resources\Packages\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackageFeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'packageFeatures';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('feature_text')
                    ->label('Feature Description')
                    ->placeholder('e.g., Full day coverage')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('feature_text')
            ->columns([
                TextColumn::make('feature_text')
                    ->label('Feature')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

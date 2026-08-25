<?php

namespace App\Filament\Resources\SiteSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studio_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_email')
                    ->label('Contact Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('contact_phone')
                    ->label('Contact Phone'),
                TextColumn::make('instagram_url')
                    ->label('Instagram')
                    ->url(fn ($record) => $record->instagram_url ?? '#', true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

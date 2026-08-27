<?php

namespace App\Filament\Resources\Galleries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('client_name')->searchable()->sortable(),
            TextColumn::make('event_name')->searchable()->sortable(),
            TextColumn::make('event_date')->date()->sortable(),
            TextColumn::make('status')
                ->state(fn ($record): string => $record->is_active && (! $record->expires_at || $record->expires_at->isFuture()) ? 'Active' : 'Expired')
                ->badge()
                ->color(fn (string $state): string => $state === 'Active' ? 'success' : 'danger'),
            TextColumn::make('client_link')
                ->label('Client Gallery Link')
                ->state(fn ($record): string => url('/gallery/' . $record->access_token))
                ->copyable()
                ->copyMessage('Gallery link copied'),
        ])->recordActions([
            EditAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ])->filters([
            Filter::make('active')
                ->label('Active Galleries')
                ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            Filter::make('expiring_soon')
                ->label('Expiring Within 14 Days')
                ->query(fn (Builder $query): Builder => $query
                    ->where('is_active', true)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now()->addDays(14))),
        ])->defaultSort('created_at', 'desc');
    }
}

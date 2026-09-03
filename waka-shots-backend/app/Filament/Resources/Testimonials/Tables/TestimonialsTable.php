<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Clients submit reviews from their gallery at any time.
            ->poll('15s')
            ->columns([
                TextColumn::make('gallery.client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gallery.event_name')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int|string|null $state): string => str_repeat('★', (int) $state))
                    ->color('warning'),
                TextColumn::make('quote')
                    ->limit(70)
                    ->tooltip(fn (Testimonial $record): string => $record->quote),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                BooleanColumn::make('is_featured')
                    ->label('Featured'),
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->disk('r2')
                    ->checkFileExistence(false)
                    ->size(48),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Testimonial $record): bool => $record->update(['status' => 'approved'])),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Testimonial $record): bool => $record->update(['status' => 'rejected'])),
                EditAction::make()
                    ->modalHeading('Review Testimonial')
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Save Moderation Changes'),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('gallery')
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at'));
    }
}

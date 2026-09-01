<?php

namespace App\Filament\Resources\PortfolioItems\Tables;

use App\Jobs\UploadPortfolioImage;
use App\Models\PortfolioItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PortfolioItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll(fn (): ?string => static::hasPendingBulkUploads() ? '3s' : null)
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('r2')
                    ->checkFileExistence(false)
                    ->size(80),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, PortfolioItem $record): string => $state ?: $record->category->name),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(fn (PortfolioItem $record): string => 'Edit: ' . ($record->title ?: $record->category->name))
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Save Changes'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected static function hasPendingBulkUploads(): bool
    {
        $jobBasename = class_basename(UploadPortfolioImage::class);

        return DB::table('jobs')
            ->where('payload', 'like', '%"displayName":"%' . $jobBasename . '"%')
            ->exists();
    }
}

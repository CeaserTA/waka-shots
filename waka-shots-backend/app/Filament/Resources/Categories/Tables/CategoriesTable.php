<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('used_by')
                    ->label('Used by')
                    ->state(fn (Category $record): string => sprintf(
                        '%d portfolio · %d journal · %d films',
                        $record->portfolio_items_count,
                        $record->journal_posts_count,
                        $record->films_count,
                    )),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(fn (Category $record): string => 'Edit: ' . $record->name)
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Save Changes'),
                self::deleteAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action): void {
                            $blocked = $records->filter(fn (Category $category): bool => self::usageCount($category) > 0);

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Categories cannot be deleted')
                                    ->body(self::blockedMessage($blocked->first()))
                                    ->danger()
                                    ->send();

                                $action->halt();

                                return;
                            }

                            $records->each->delete();
                        }),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount([
                'portfolioItems',
                'journalPosts',
                'films',
            ]))
            ->defaultSort('name');
    }

    private static function deleteAction(): \Filament\Actions\DeleteAction
    {
        return \Filament\Actions\DeleteAction::make()
            ->action(function (Category $record, \Filament\Actions\DeleteAction $action): void {
                if (self::usageCount($record) > 0) {
                    Notification::make()
                        ->title('Category cannot be deleted')
                        ->body(self::blockedMessage($record))
                        ->danger()
                        ->send();

                    $action->halt();

                    return;
                }

                $record->delete();
            });
    }

    private static function usageCount(Category $category): int
    {
        return $category->portfolio_items_count
            + $category->journal_posts_count
            + $category->films_count;
    }

    private static function blockedMessage(Category $category): string
    {
        return sprintf(
            'Cannot delete "%s" because it is used by %d portfolio items, %d journal posts, and %d films.',
            $category->name,
            $category->portfolio_items_count,
            $category->journal_posts_count,
            $category->films_count,
        );
    }
}

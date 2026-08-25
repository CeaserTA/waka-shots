<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->action(function (Category $record, DeleteAction $action): void {
                    $usage = $record->portfolioItems()->count()
                        + $record->journalPosts()->count()
                        + $record->films()->count();

                    if ($usage > 0) {
                        Notification::make()
                            ->title('Category cannot be deleted')
                            ->body(sprintf(
                                'Cannot delete "%s": %d portfolio items, %d journal posts, and %d films use this category.',
                                $record->name,
                                $record->portfolioItems()->count(),
                                $record->journalPosts()->count(),
                                $record->films()->count(),
                            ))
                            ->danger()
                            ->send();

                        $action->halt();

                        return;
                    }

                    $record->delete();
                }),
        ];
    }
}

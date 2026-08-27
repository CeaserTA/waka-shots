<?php

namespace App\Filament\Resources\PortfolioItems\Pages;

use App\Filament\Resources\PortfolioItems\PortfolioItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePortfolioItems extends ManageRecords
{
    protected static string $resource = PortfolioItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('New Portfolio Item')
                ->modalHeading('New Portfolio Item')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Create Portfolio Item'),
        ];
    }
}

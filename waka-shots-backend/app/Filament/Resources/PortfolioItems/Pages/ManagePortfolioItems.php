<?php

namespace App\Filament\Resources\PortfolioItems\Pages;

use App\Filament\Resources\PortfolioItems\PortfolioItemResource;
use App\Models\PortfolioItem;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

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
            Action::make('bulkUpload')
                ->label('Bulk Upload')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalHeading('Bulk Upload Portfolio Images')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Upload All')
                ->form([
                    Select::make('category_id')
                        ->label('Category (applies to every image in this batch)')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    FileUpload::make('images')
                        ->label('Images')
                        ->multiple()
                        ->reorderable()
                        ->disk('r2')
                        ->visibility('public')
                        ->directory('portfolio-images')
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->imageEditor()
                        ->required()
                        ->maxSize(10240),
                ])
                ->action(function (array $data): void {
                    foreach ($data['images'] as $imagePath) {
                        PortfolioItem::create([
                            'category_id' => $data['category_id'],
                            'image_path' => $imagePath,
                            'title' => null,
                        ]);
                    }
                })
                ->successNotificationTitle('Images uploaded successfully'),
        ];
    }
}

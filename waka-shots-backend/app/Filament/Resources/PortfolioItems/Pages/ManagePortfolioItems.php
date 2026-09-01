<?php

namespace App\Filament\Resources\PortfolioItems\Pages;

use App\Filament\Resources\PortfolioItems\PortfolioItemResource;
use App\Jobs\UploadPortfolioImage;
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
                        ->disk('local')
                        ->directory('portfolio-uploads-tmp')
                        ->fetchFileInformation(false)
                        ->acceptedFileTypes(['image/*'])
                        ->image()
                        ->imageEditor()
                        ->automaticallyResizeImagesToWidth(2500)
                        ->automaticallyResizeImagesMode('contain')
                        ->imageResizeUpscale(false)
                        ->required()
                        ->maxSize(10240),
                ])
                ->action(function (array $data): void {
                    foreach ($data['images'] as $imagePath) {
                        UploadPortfolioImage::dispatch($imagePath, (int) $data['category_id']);
                    }
                })
                ->successNotificationTitle('Images queued — they will appear in the table shortly'),
        ];
    }
}

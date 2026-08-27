<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\JournalPosts\JournalPostResource;
use App\Filament\Resources\PortfolioItems\PortfolioItemResource;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'actions' => [
                [
                    'label' => 'New Portfolio Item',
                    'url' => PortfolioItemResource::getUrl('index'),
                    'icon' => 'heroicon-o-image',
                ],
                [
                    'label' => 'New Gallery',
                    'url' => GalleryResource::getUrl('create'),
                    'icon' => 'heroicon-o-photo',
                ],
                [
                    'label' => 'New Journal Post',
                    'url' => JournalPostResource::getUrl('create'),
                    'icon' => 'heroicon-o-document-text',
                ],
            ],
        ];
    }
}


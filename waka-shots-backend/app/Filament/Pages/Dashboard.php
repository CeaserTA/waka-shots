<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\QuickStats;
use App\Filament\Widgets\RecentActivityWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getSubheading(): ?string
    {
        return 'Overview of your site and client galleries';
    }

    public function getWidgets(): array
    {
        return [
            QuickActionsWidget::class,
            QuickStats::class,
            RecentActivityWidget::class,
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\GoogleDriveConnection as GoogleDriveConnectionModel;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickStats extends StatsOverviewWidget
{
    protected ?string $heading = null;

    protected function getStats(): array
    {
        $now = now();
        $activeGalleries = Gallery::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', $now))
            ->count();
        $expiringSoon = Gallery::query()
            ->where('is_active', true)
            ->whereBetween('expires_at', [$now, $now->copy()->addDays(7)])
            ->count();
        $driveConnected = GoogleDriveConnectionModel::query()
            ->whereNotNull('refresh_token')
            ->exists();
        $newEnquiries = Enquiry::query()->where('status', 'new')->count();

        return [
            Stat::make('Active Galleries', $activeGalleries)
                ->icon(Heroicon::OutlinedPhoto)
                ->color('primary')
                ->extraAttributes(['class' => 'fi-stat-border-accent-gold'])
                ->url(GalleryResource::getUrl(parameters: ['tableFilters' => ['active' => ['isActive' => true]]])),
            Stat::make('Expiring Soon', $expiringSoon)
                ->color($expiringSoon > 0 ? 'warning' : 'gray')
                ->icon($expiringSoon > 0 ? Heroicon::OutlinedClock : Heroicon::OutlinedCheckCircle)
                ->extraAttributes(['class' => $expiringSoon > 0 ? 'fi-stat-border-accent-warning' : 'fi-stat-border-accent-gray'])
                ->url(GalleryResource::getUrl(parameters: ['tableFilters' => ['expiring_soon' => ['isActive' => true]]])),
            Stat::make('Google Drive Connection', $driveConnected ? 'Connected' : 'Disconnected')
                ->color($driveConnected ? 'success' : 'danger')
                ->icon($driveConnected ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedExclamationTriangle)
                ->extraAttributes(['class' => $driveConnected ? 'fi-stat-border-accent-success' : 'fi-stat-border-accent-danger'])
                ->url(GalleryResource::getUrl()),
            Stat::make('New Enquiries', $newEnquiries)
                ->color($newEnquiries > 0 ? 'primary' : 'gray')
                ->icon(Heroicon::OutlinedInbox)
                ->extraAttributes(['class' => $newEnquiries > 0 ? 'fi-stat-border-accent-gold' : 'fi-stat-border-accent-gray']),
        ];
    }
}

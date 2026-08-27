<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\Enquiry;
use App\Models\GalleryAccessLog;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-activity-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $enquiries = Enquiry::query()
            ->with('service')
            ->where('status', 'new')
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (Enquiry $enquiry): array => [
                'type' => 'enquiry',
                'label' => 'Enquiry',
                'description' => $this->enquiryDescription($enquiry),
                'timestamp' => $enquiry->created_at,
                'link' => EnquiryResource::getUrl('view', ['record' => $enquiry]),
            ]);

        $galleryActivity = GalleryAccessLog::query()
            ->with('gallery')
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (GalleryAccessLog $log): array => [
                'type' => 'gallery',
                'label' => 'Gallery activity',
                'description' => $this->galleryDescription($log),
                'timestamp' => $log->created_at,
                'link' => $log->gallery
                    ? GalleryResource::getUrl('edit', ['record' => $log->gallery])
                    : GalleryResource::getUrl(),
            ]);

        return [
            'activities' => $enquiries
                ->concat($galleryActivity)
                ->sortByDesc('timestamp')
                ->take(10)
                ->values(),
        ];
    }

    private function enquiryDescription(Enquiry $enquiry): string
    {
        $service = $enquiry->service?->name;

        return $service
            ? "{$enquiry->name} sent a new {$service} enquiry"
            : "{$enquiry->name} sent a new enquiry";
    }

    private function galleryDescription(GalleryAccessLog $log): string
    {
        $gallery = $log->gallery;
        $activity = match ($log->event_type) {
            'view' => 'viewed',
            'download' => 'downloaded a photo from',
            'download_all' => 'downloaded all photos from',
            default => $log->event_type,
        };

        return $gallery
            ? "{$gallery->client_name} {$activity} {$gallery->event_name}"
            : "Gallery activity: {$activity}";
    }
}

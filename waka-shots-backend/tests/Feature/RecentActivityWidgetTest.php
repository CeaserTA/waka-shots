<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\GalleryAccessLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentActivityWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_contains_quick_actions_stats_and_merged_activity_feed(): void
    {
        $this->assertSame([
            QuickActionsWidget::class,
            'App\\Filament\\Widgets\\QuickStats',
            RecentActivityWidget::class,
        ], app(Dashboard::class)->getWidgets());
    }

    public function test_activity_feed_merges_enquiries_and_gallery_logs_chronologically(): void
    {
        $gallery = Gallery::create([
            'client_name' => 'Gallery Client',
            'event_name' => 'Wedding',
            'event_date' => '2026-09-01',
            'drive_folder_id' => 'folder_123',
        ]);
        $enquiry = Enquiry::create([
            'status' => 'new',
            'name' => 'Enquiry Client',
            'email' => 'client@example.com',
        ]);
        $enquiry->created_at = Carbon::now()->subMinutes(5);
        $enquiry->save();
        $log = GalleryAccessLog::create([
            'gallery_id' => $gallery->id,
            'event_type' => 'download_all',
            'ip_address' => '127.0.0.1',
        ]);
        $log->created_at = Carbon::now()->subMinute();
        $log->save();

        $method = new \ReflectionMethod(RecentActivityWidget::class, 'getViewData');
        $method->setAccessible(true);
        $data = $method->invoke(app(RecentActivityWidget::class));
        $activities = $data['activities'];

        $this->assertCount(2, $activities);
        $this->assertSame('Gallery Client downloaded all photos from Wedding', $activities[0]['description']);
        $this->assertSame('Enquiry Client sent a new enquiry', $activities[1]['description']);
        $this->assertSame(GalleryResource::getUrl('edit', ['record' => $gallery]), $activities[0]['link']);
    }
}


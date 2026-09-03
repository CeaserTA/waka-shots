<?php

namespace App\Observers;

use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\Testimonial;
use App\Support\AdminNotifier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class TestimonialObserver
{
    public function created(Testimonial $testimonial): void
    {
        $client = $testimonial->gallery?->client_name;

        AdminNotifier::send(
            Notification::make()
                ->title('New review submitted')
                ->body(trim(($client ? $client . ' · ' : '') . str_repeat('★', (int) $testimonial->rating) . ' — awaiting approval'))
                ->icon('heroicon-o-star')
                ->success()
                ->actions([
                    Action::make('review')
                        ->label('Review it')
                        ->url(TestimonialResource::getUrl())
                        ->markAsRead(),
                ])
        );
    }
}

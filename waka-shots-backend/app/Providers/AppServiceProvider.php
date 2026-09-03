<?php

namespace App\Providers;

use App\Models\Enquiry;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Observers\EnquiryObserver;
use App\Observers\TestimonialObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $view->with('siteSetting', once(fn () => SiteSetting::current()));
        });

        Enquiry::observe(EnquiryObserver::class);
        Testimonial::observe(TestimonialObserver::class);

        RateLimiter::for('gallery-downloads', function (Request $request) {
            return Limit::perMinutes(10, 60)->by($request->route('token') . '|' . $request->ip());
        });

        // Thumbnails are a small proxied fetch (no Drive alt=media call), and
        // the lightbox may eagerly preload most of a gallery in the
        // background — a much more generous ceiling than the full-original
        // preview/download routes above.
        RateLimiter::for('gallery-thumbnails', function (Request $request) {
            return Limit::perMinute(120)->by($request->route('token') . '|' . $request->ip());
        });

        RateLimiter::for('gallery-testimonials', function (Request $request) {
            return Limit::perHour(5)->by((string) $request->route('token') . '|' . $request->ip());
        });

        RateLimiter::for('gallery-download-all', function (Request $request) {
            return Limit::perHour(5)->by((string) $request->route('token'));
        });
    }
}

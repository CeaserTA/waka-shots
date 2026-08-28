<?php

namespace App\Providers;

use App\Models\SiteSetting;
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
        RateLimiter::for('gallery-downloads', function (Request $request) {
            return Limit::perMinutes(10, 60)->by($request->route('token') . '|' . $request->ip());
        });

        RateLimiter::for('gallery-testimonials', function (Request $request) {
            return Limit::perHour(5)->by((string) $request->route('token') . '|' . $request->ip());
        });

        RateLimiter::for('gallery-download-all', function (Request $request) {
            return Limit::perHour(5)->by((string) $request->route('token'));
        });

        View::composer('*', function ($view): void {
            $view->with('siteSetting', SiteSetting::first());
        });
    }
}

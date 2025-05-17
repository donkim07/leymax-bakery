<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Services\ThemeService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ThemeService as a singleton
        $this->app->singleton(ThemeService::class, function ($app) {
            return new ThemeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for MySQL < 5.7.7 and MariaDB < 10.2.2
        Schema::defaultStringLength(191);

        // Set timezone
        date_default_timezone_set(config('app.timezone', 'UTC'));

        // Force HTTPS in production
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}

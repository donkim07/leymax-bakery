<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Add route model binding patterns
        Route::pattern('business', '[0-9]+');
        Route::pattern('store', '[0-9]+');
        Route::pattern('product', '[0-9]+');
        Route::pattern('category', '[0-9]+');

        // Add explicit route model bindings with authorization
        Route::bind('business', function ($value) {
            return Business::where('id', $value)
                ->where(function($query) {
                    $query->where('created_by', auth()->id())
                        ->orWhereHas('users', function($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->firstOrFail();
        });

        Route::bind('store', function ($value) {
            return Store::where('id', $value)
                ->whereHas('business', function($query) {
                    $query->where('created_by', auth()->id())
                        ->orWhereHas('users', function($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->firstOrFail();
        });

        Route::bind('product', function ($value) {
            return Product::where('id', $value)
                ->whereHas('business', function($query) {
                    $query->where('created_by', auth()->id())
                        ->orWhereHas('users', function($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->firstOrFail();
        });

        // Configure rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
} 
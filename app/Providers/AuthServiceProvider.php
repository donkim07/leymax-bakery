<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Define your model-policy mappings here
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for business-specific permissions
        Gate::define('access-bakery', function ($user) {
            return $user->hasPermission('bakery.view');
        });

        Gate::define('access-tools', function ($user) {
            return $user->hasPermission('tools.view');
        });

        Gate::define('access-academy', function ($user) {
            return $user->hasPermission('academy.view');
        });

        // Define gates for role-based permissions
        Gate::define('manage-users', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-roles', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-settings', function ($user) {
            return $user->hasRole(['admin', 'manager']);
        });

        Gate::define('view-reports', function ($user) {
            return $user->hasRole(['admin', 'manager', 'accountant']);
        });
    }
} 
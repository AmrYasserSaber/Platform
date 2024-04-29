<?php

namespace App\Providers;

use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerPolicies();

        Gate::define('viewAny', [UserPolicy::class, 'viewAny']);
        Gate::define('view', [UserPolicy::class, 'view']);
        Gate::define('create', [UserPolicy::class, 'create']);
        Gate::define('update', [UserPolicy::class, 'update']);
        Gate::define('delete', [UserPolicy::class, 'delete']);
        Gate::define('restore', [UserPolicy::class, 'restore']);
        Gate::define('forceDelete', [UserPolicy::class, 'forceDelete']);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

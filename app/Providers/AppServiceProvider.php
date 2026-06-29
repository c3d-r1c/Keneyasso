<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\SidebarRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SidebarRegistry::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Politique globale : le rôle admin bypass tous les can() de l'application,
        // quel que soit l'état des modules. À garder dans le core, pas dans un module.
        Gate::before(static fn ($user) => $user->hasRole('admin') ? true : null);
    }
}

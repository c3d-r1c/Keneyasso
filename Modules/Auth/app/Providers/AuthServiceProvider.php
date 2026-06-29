<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use App\Services\SidebarItem;
use App\Services\SidebarRegistry;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Auth\Http\Livewire\RoleForm;
use Modules\Auth\Http\Livewire\RoleTable;

/**
 * Point d'entrée du module Auth.
 *
 * Gère les rôles, permissions et profils utilisateurs via Spatie Permission.
 * Enregistré automatiquement par nwidart via module.json.
 */
final class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'auth');

        Livewire::component('auth.role-table', RoleTable::class);
        Livewire::component('auth.role-form', RoleForm::class);

        $this->app->make(SidebarRegistry::class)->register(new SidebarItem(
            label: 'Administration',
            route: '',
            icon: 'icon-Settings-1',
            order: 90,
            children: [
                new SidebarItem(
                    label: 'Rôles & permissions',
                    route: 'auth.roles.index',
                    icon: 'icon-Lock-overturning',
                ),
            ],
        ));
    }
}

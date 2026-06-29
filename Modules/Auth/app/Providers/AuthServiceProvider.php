<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use App\Services\SidebarItem;
use App\Services\SidebarRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Auth\Domain\UtilisateurRepository;
use Modules\Auth\Http\Livewire\RoleTable;
use Modules\Auth\Http\Livewire\UserTable;
use Modules\Auth\Repositories\EloquentUtilisateurRepository;

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
        Route::middleware('web')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        });
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'auth');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'auth');

        Livewire::component('auth.role-table', RoleTable::class);
        Livewire::component('auth.user-table', UserTable::class);

        $this->app->bind(UtilisateurRepository::class, EloquentUtilisateurRepository::class);

        $this->app->make(SidebarRegistry::class)->register(new SidebarItem(
            label: __('menu.administration'),
            route: '',
            icon: 'icon-Settings-1',
            order: 90,
            children: [
                new SidebarItem(
                    label: __('menu.roles'),
                    route: 'auth.roles.index',
                    icon: 'icon-Lock-overturning',
                    permission: 'gérer rôles',
                ),
                new SidebarItem(
                    label: __('menu.utilisateurs'),
                    route: 'auth.users.index',
                    icon: 'icon-Add-user',
                    permission: 'gérer utilisateurs',
                ),
            ],
        ));
    }
}

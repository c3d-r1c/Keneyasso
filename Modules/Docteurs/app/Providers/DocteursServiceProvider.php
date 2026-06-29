<?php

declare(strict_types=1);

namespace Modules\Docteurs\Providers;

use App\Services\SidebarItem;
use App\Services\SidebarRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Docteurs\Domain\DocteurRepository;
use Modules\Docteurs\Http\Livewire\DocteurTable;
use Modules\Docteurs\Repositories\EloquentDocteurRepository;

/**
 * Point d'entrée du module Docteurs.
 *
 * Enregistré automatiquement par nwidart via module.json — ne pas ajouter
 * dans bootstrap/providers.php. Pour désactiver le module, modifier
 * modules_statuses.json ou utiliser `php artisan module:disable Docteurs`.
 *
 * Charge de manière autonome :
 * - Les routes du module (routes/web.php)
 * - Le binding DocteurRepository → EloquentDocteurRepository
 * Les migrations sont auto-découvertes par nwidart (auto-discover.migrations = true).
 */
final class DocteursServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DocteurRepository::class, EloquentDocteurRepository::class);
    }

    public function boot(): void
    {
        Route::middleware('web')->group(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        });
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'docteurs');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'docteurs');

        Livewire::component('docteurs.docteur-table', DocteurTable::class);

        $this->app->make(SidebarRegistry::class)->register(new SidebarItem(
            label: __('menu.medecins'),
            route: 'doclinic.doctor_list',
            icon: 'icon-Diagnostics',
            order: 20,
            homeComponent: 'docteurs.docteur-table',
            permission: 'voir docteurs',
        ));
    }
}

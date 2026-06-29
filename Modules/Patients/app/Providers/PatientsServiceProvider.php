<?php

declare(strict_types=1);

namespace Modules\Patients\Providers;

use App\Services\SidebarItem;
use App\Services\SidebarRegistry;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Patients\Domain\PatientRepository;
use Modules\Patients\Http\Livewire\PatientTable;
use Modules\Patients\Repositories\EloquentPatientRepository;

/**
 * Point d'entrée du module Patients.
 *
 * Enregistré automatiquement par nwidart via module.json — ne pas ajouter
 * dans bootstrap/providers.php. Pour désactiver le module, modifier
 * modules_statuses.json ou utiliser `php artisan module:disable Patients`.
 *
 * Charge de manière autonome :
 * - Les routes du module (routes/web.php)
 * - Le binding PatientRepository → EloquentPatientRepository
 * Les migrations sont auto-découvertes par nwidart (auto-discover.migrations = true).
 */
final class PatientsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PatientRepository::class, EloquentPatientRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'patients');

        Livewire::component('patients.patient-table', PatientTable::class);

        $this->app->make(SidebarRegistry::class)->register(new SidebarItem(
            label: 'Patients',
            route: 'doclinic.patients',
            icon: 'icon-Compiling',
            order: 10,
            homeComponent: 'patients.patient-table',
        ));
    }
}

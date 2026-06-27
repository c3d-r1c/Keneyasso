<?php

declare(strict_types=1);

namespace Modules\Patients\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Patients\Domain\PatientRepository;
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
    }
}

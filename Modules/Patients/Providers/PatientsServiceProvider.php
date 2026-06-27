<?php

declare(strict_types=1);

namespace Modules\Patients\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Patients\Domain\PatientRepository;
use Modules\Patients\Infrastructure\Persistence\EloquentPatientRepository;

/**
 * Point d'entrée du module Patients.
 *
 * Ce ServiceProvider est le seul fichier à enregistrer dans bootstrap/providers.php.
 * Il charge de manière autonome :
 * - Les migrations du module (Infrastructure/database/migrations/)
 * - Les routes du module (Presentation/routes/web.php)
 * - Le binding PatientRepository → EloquentPatientRepository
 *
 * Pour désactiver le module entièrement : retirer ce provider de bootstrap/providers.php.
 */
final class PatientsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PatientRepository::class, EloquentPatientRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Infrastructure/database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../Presentation/routes/web.php');
    }
}

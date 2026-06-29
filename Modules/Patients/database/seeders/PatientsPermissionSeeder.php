<?php

declare(strict_types=1);

namespace Modules\Patients\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Initialise les permissions du module Patients en base.
 *
 * À appeler via : php artisan db:seed --class="Modules\Patients\Database\Seeders\PatientsPermissionSeeder"
 * ou depuis DatabaseSeeder.
 */
final class PatientsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'voir patients',
            'inscrire patient',
            'modifier patient',
            'supprimer patient',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}

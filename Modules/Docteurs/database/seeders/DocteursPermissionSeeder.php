<?php

declare(strict_types=1);

namespace Modules\Docteurs\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Initialise les permissions du module Docteurs en base.
 *
 * À appeler via : php artisan db:seed --class="Modules\Docteurs\Database\Seeders\DocteursPermissionSeeder"
 * ou depuis DatabaseSeeder.
 */
final class DocteursPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'voir médecins',
            'inscrire médecin',
            'modifier médecin',
            'supprimer médecin',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}

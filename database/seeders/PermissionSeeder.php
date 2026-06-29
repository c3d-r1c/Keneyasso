<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Initialise les permissions de l'application et les assigne au rôle admin.
 *
 * À relancer après l'ajout d'un nouveau module : toutes les permissions
 * nouvellement créées sont automatiquement ajoutées à l'admin.
 */
class PermissionSeeder extends Seeder
{
    /** @var list<string> */
    private const PERMISSIONS = [
        'voir patients',
        'voir docteurs',
        'gérer rôles',
        'gérer utilisateurs',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(self::PERMISSIONS);
    }
}

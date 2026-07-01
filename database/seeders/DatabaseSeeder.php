<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crée toutes les permissions + rôle admin
        $this->call(PermissionSeeder::class);

        // 2. Admin — accès total via Gate::before()
        $admin = User::factory()->create([
            'name'  => 'Super Admin',
            'email' => 'admin@keneyasso.bf',
            'password' => Hash::make('Admin@123'),
        ]);
        $admin->assignRole('admin');

        // 3. Infirmier — accès limité (voir patients uniquement)
        $infirmier = Role::firstOrCreate(['name' => 'infirmier', 'guard_name' => 'web']);
        $infirmier->givePermissionTo('voir patients');

        $user = User::factory()->create([
            'name'  => 'Fatoumata Diallo',
            'email' => 'infirmier@keneyasso.bf',
            'password' => Hash::make('Admin@123'),
        ]);
        $user->assignRole('infirmier');

        // 4. Sans rôle — connecté mais bloqué partout
        User::factory()->create([
            'name'  => 'Utilisateur Bloqué',
            'email' => 'bloque@keneyasso.bf',
            'password' => Hash::make('Admin@123'),
        ]);
    }
}

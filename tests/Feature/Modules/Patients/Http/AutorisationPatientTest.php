<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Vérifie que les routes du module Patients respectent les permissions.
 *
 * Règle métier : seuls les utilisateurs ayant « voir patients »
 * peuvent accéder au module (liste et création).
 */

// ─── Liste ────────────────────────────────────────────────────────────────────

it('un invité est redirigé vers login', function (): void {
    $this->get(route('doclinic.patients'))
        ->assertRedirect(route('login'));
});

it('un utilisateur sans permission ne peut pas voir les patients', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('doclinic.patients'))
        ->assertForbidden();
});

it('un utilisateur avec « voir patients » peut voir la liste', function (): void {
    Permission::create(['name' => 'voir patients', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->givePermissionTo('voir patients');

    $this->actingAs($user)
        ->get(route('doclinic.patients'))
        ->assertOk();
});

// ─── Création ─────────────────────────────────────────────────────────────────

it('un utilisateur sans permission ne peut pas inscrire un patient', function (): void {
    $this->actingAs(User::factory()->create())
        ->post(route('doclinic.patients.store'), [
            'prenom' => 'Moussa',
            'nom_de_famille' => 'Traoré',
            'date_de_naissance' => '1990-05-15',
        ])
        ->assertForbidden();
});

it('un admin peut inscrire un patient sans permission explicite', function (): void {
    $role  = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($role);

    $this->actingAs($admin)
        ->post(route('doclinic.patients.store'), [
            'prenom' => 'Moussa',
            'nom_de_famille' => 'Traoré',
            'date_de_naissance' => '1990-05-15',
        ])
        ->assertRedirect();
});

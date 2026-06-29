<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Vérifie que les routes du module Auth respectent les permissions Spatie.
 *
 * Règle métier protégée :
 * - Un utilisateur sans permission « gérer rôles » ne peut pas accéder à la gestion des rôles.
 * - Un utilisateur sans permission « gérer utilisateurs » ne peut pas accéder aux utilisateurs.
 * - Un admin (Gate::before bypass) accède à tout sans permission explicite.
 */

// ─── Helpers ──────────────────────────────────────────────────────────────────

function creerPermission(string $nom): Permission
{
    return Permission::firstOrCreate(['name' => $nom, 'guard_name' => 'web']);
}

function utilisateurAvecPermission(string $nom): User
{
    creerPermission($nom);
    $user = User::factory()->create();
    $user->givePermissionTo($nom);

    return $user;
}

// ─── Rôles & permissions ──────────────────────────────────────────────────────

it('un utilisateur sans permission ne peut pas accéder à la gestion des rôles', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('auth.roles.index'))
        ->assertForbidden();
});

it('un utilisateur avec « gérer rôles » peut accéder à la gestion des rôles', function (): void {
    $this->actingAs(utilisateurAvecPermission('gérer rôles'))
        ->get(route('auth.roles.index'))
        ->assertOk();
});

// ─── Utilisateurs ─────────────────────────────────────────────────────────────

it('un utilisateur sans permission ne peut pas voir la liste des utilisateurs', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('auth.users.index'))
        ->assertForbidden();
});

it('un utilisateur avec « gérer utilisateurs » peut voir la liste', function (): void {
    $this->actingAs(utilisateurAvecPermission('gérer utilisateurs'))
        ->get(route('auth.users.index'))
        ->assertOk();
});

it('un utilisateur sans permission ne peut pas créer un utilisateur', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('auth.users.create'))
        ->assertForbidden();
});

it('un utilisateur sans permission ne peut pas supprimer un utilisateur', function (): void {
    $cible = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('auth.users.destroy', $cible))
        ->assertForbidden();
});

// ─── Bypass admin ─────────────────────────────────────────────────────────────

it('un admin accède aux rôles sans permission explicite', function (): void {
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($role);

    $this->actingAs($admin)
        ->get(route('auth.roles.index'))
        ->assertOk();
});

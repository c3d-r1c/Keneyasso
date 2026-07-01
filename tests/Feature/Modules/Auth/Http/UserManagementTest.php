<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests HTTP pour la gestion des utilisateurs (liste, édition, suppression).
 *
 * Fixture : un utilisateur connecté avec un rôle Spatie est utilisé
 * comme acteur dans tous les tests authentifiés.
 *
 * Règle métier protégée : seuls les utilisateurs authentifiés peuvent
 * gérer les comptes ; les invités sont redirigés vers /login.
 */

// ─── Helpers ──────────────────────────────────────────────────────────────────

function utilisateurConnecte(): User
{
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

// ─── Liste ────────────────────────────────────────────────────────────────────

it('un invité ne peut pas voir la liste des utilisateurs', function (): void {
    $this->get(route('auth.users.index'))
        ->assertRedirect(route('login'));
});

it('un utilisateur connecté peut voir la liste des utilisateurs', function (): void {
    $this->actingAs(utilisateurConnecte())
        ->get(route('auth.users.index'))
        ->assertOk();
});

// ─── Édition ──────────────────────────────────────────────────────────────────

it('un invité ne peut pas voir le formulaire d\'édition', function (): void {
    $user = User::factory()->create();

    $this->get(route('auth.users.edit', $user))
        ->assertRedirect(route('login'));
});

it('un utilisateur connecté peut voir le formulaire d\'édition', function (): void {
    $cible = User::factory()->create();

    $this->actingAs(utilisateurConnecte())
        ->get(route('auth.users.edit', $cible))
        ->assertOk()
        ->assertSee($cible->name);
});

// ─── Mise à jour ──────────────────────────────────────────────────────────────

it('un invité ne peut pas mettre à jour un utilisateur', function (): void {
    $user = User::factory()->create();

    $this->put(route('auth.users.update', $user), [])
        ->assertRedirect(route('login'));
});

it('un admin peut modifier le nom et l\'email d\'un utilisateur', function (): void {
    $role = Role::firstOrCreate(['name' => 'médecin', 'guard_name' => 'web']);
    $cible = User::factory()->create(['name' => 'Ancien Nom', 'email' => 'ancien@hopital.ml']);
    $cible->assignRole($role);

    $this->actingAs(utilisateurConnecte())
        ->put(route('auth.users.update', $cible), [
            'nom' => 'Nouveau Nom',
            'email' => 'nouveau@hopital.ml',
            'role_id' => $role->id,
        ])
        ->assertRedirect(route('auth.users.index'));

    expect($cible->fresh()->name)->toBe('Nouveau Nom')
        ->and($cible->fresh()->email)->toBe('nouveau@hopital.ml');
});

it('l\'email doit être unique sauf pour l\'utilisateur lui-même', function (): void {
    $role = Role::firstOrCreate(['name' => 'médecin', 'guard_name' => 'web']);
    $cible = User::factory()->create(['email' => 'moi@hopital.ml']);
    $cible->assignRole($role);

    // Modifier avec son propre email ne doit pas échouer
    $this->actingAs(utilisateurConnecte())
        ->put(route('auth.users.update', $cible), [
            'nom' => $cible->name,
            'email' => 'moi@hopital.ml',
            'role_id' => $role->id,
        ])
        ->assertRedirect(route('auth.users.index'));
});

it('l\'email ne peut pas être celui d\'un autre utilisateur', function (): void {
    $role = Role::firstOrCreate(['name' => 'médecin', 'guard_name' => 'web']);
    User::factory()->create(['email' => 'pris@hopital.ml']);
    $cible = User::factory()->create();
    $cible->assignRole($role);

    $this->actingAs(utilisateurConnecte())
        ->put(route('auth.users.update', $cible), [
            'nom' => $cible->name,
            'email' => 'pris@hopital.ml',
            'role_id' => $role->id,
        ])
        ->assertSessionHasErrors('email');
});

// ─── Suppression ──────────────────────────────────────────────────────────────

it('un invité ne peut pas supprimer un utilisateur', function (): void {
    $user = User::factory()->create();

    $this->delete(route('auth.users.destroy', $user))
        ->assertRedirect(route('login'));
});

it('un admin peut supprimer un utilisateur', function (): void {
    $cible = User::factory()->create();

    $this->actingAs(utilisateurConnecte())
        ->delete(route('auth.users.destroy', $cible))
        ->assertRedirect(route('auth.users.index'));

    expect(User::find($cible->id))->toBeNull();
});

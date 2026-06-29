<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Auth\Http\Livewire\RoleTable;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests du composant Livewire RoleTable.
 *
 * Fixture : le composant liste les rôles depuis la table roles (Spatie).
 * Il permet de chercher un rôle par nom et de le supprimer.
 *
 * Règle métier protégée : la gestion des rôles est réservée au module Auth ;
 * aucun autre module ne modifie la table roles directement.
 */

// ─── Rendu initial ────────────────────────────────────────────────────────────

it('rend le composant RoleTable sans erreur', function (): void {
    Livewire::test(RoleTable::class)
        ->assertStatus(200);
});

it('affiche les rôles existants', function (): void {
    Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    Role::create(['name' => 'infirmier', 'guard_name' => 'web']);

    Livewire::test(RoleTable::class)
        ->assertSee('médecin')
        ->assertSee('infirmier');
});

it('affiche un message quand aucun rôle n\'existe', function (): void {
    Livewire::test(RoleTable::class)
        ->assertSee('Aucun rôle');
});

// ─── Recherche ────────────────────────────────────────────────────────────────

it('filtre les rôles par nom', function (): void {
    Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    Role::create(['name' => 'infirmier', 'guard_name' => 'web']);

    Livewire::test(RoleTable::class)
        ->set('search', 'médecin')
        ->assertSee('médecin')
        ->assertDontSee('infirmier');
});

// ─── Suppression ──────────────────────────────────────────────────────────────

it('supprime un rôle existant', function (): void {
    // Un rôle supprimé depuis l'interface ne doit plus apparaître dans la liste.
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

    Livewire::test(RoleTable::class)
        ->call('delete', $role->id)
        ->assertDontSee('admin');

    expect(Role::count())->toBe(0);
});

<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Auth\Http\Livewire\RoleTable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests du formulaire de création de rôle intégré dans RoleTable.
 *
 * Fixture : le formulaire de création est intégré dans le composant RoleTable
 * (modal latéral). Les propriétés `name` et `selectedPermissions` pilotent la création.
 *
 * Règle métier protégée : un rôle doit avoir un nom unique ;
 * les permissions assignées doivent exister en base.
 */

// ─── Création ─────────────────────────────────────────────────────────────────

it('crée un rôle avec un nom valide', function (): void {
    // La création d'un rôle est l'opération fondamentale du module Auth.
    Livewire::test(RoleTable::class)
        ->set('name', 'médecin')
        ->call('save')
        ->assertHasNoErrors();

    expect(Role::where('name', 'médecin')->exists())->toBeTrue();
});

it('rejette un nom de rôle vide', function (): void {
    Livewire::test(RoleTable::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('rejette un nom de rôle déjà utilisé', function (): void {
    Role::create(['name' => 'superadmin', 'guard_name' => 'web']);

    Livewire::test(RoleTable::class)
        ->set('name', 'superadmin')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

// ─── Assignation de permissions ───────────────────────────────────────────────

it('assigne les permissions sélectionnées au rôle créé', function (): void {
    // L'objectif métier est de contrôler ce qu'un rôle peut faire dans l'app.
    $p1 = Permission::create(['name' => 'voir patients', 'guard_name' => 'web']);
    $p2 = Permission::create(['name' => 'inscrire patient', 'guard_name' => 'web']);

    Livewire::test(RoleTable::class)
        ->set('name', 'infirmier')
        ->set('selectedPermissions', [$p1->id])
        ->call('save');

    $role = Role::where('name', 'infirmier')->first();

    expect($role->permissions->pluck('id')->toArray())->toContain($p1->id)
        ->and($role->permissions->pluck('id')->toArray())->not->toContain($p2->id);
});

it('réinitialise le formulaire après une création réussie', function (): void {
    Livewire::test(RoleTable::class)
        ->set('name', 'gestionnaire')
        ->call('save')
        ->assertSet('name', '');
});

// ─── Édition des permissions ──────────────────────────────────────────────────

it('openEdit() pré-remplit le formulaire avec les données du rôle', function (): void {
    // Règle : l'admin doit retrouver l'état actuel du rôle pour le modifier.
    $p1 = Permission::create(['name' => 'voir patients', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);
    $role->givePermissionTo($p1);

    Livewire::test(RoleTable::class)
        ->call('openEdit', $role->id)
        ->assertSet('editingRoleId', $role->id)
        ->assertSet('name', 'infirmier')
        ->assertSet('selectedPermissions', [$p1->id])
        ->assertSet('showModal', true);
});

it('saveEdit() met à jour les permissions d\'un rôle existant', function (): void {
    // Règle : la mise à jour remplace exactement les permissions cochées.
    $p1 = Permission::create(['name' => 'voir patients', 'guard_name' => 'web']);
    $p2 = Permission::create(['name' => 'voir docteurs', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);
    $role->givePermissionTo($p1);

    Livewire::test(RoleTable::class)
        ->call('openEdit', $role->id)
        ->set('selectedPermissions', [$p1->id, $p2->id])
        ->call('save')
        ->assertHasNoErrors();

    expect($role->fresh()->permissions->pluck('id')->toArray())
        ->toContain($p1->id)
        ->toContain($p2->id);
});

it('save() retire une permission décochée lors de la mise à jour', function (): void {
    // Règle : syncPermissions remplace — les permissions non cochées sont retirées.
    $p1 = Permission::create(['name' => 'voir patients', 'guard_name' => 'web']);
    $p2 = Permission::create(['name' => 'voir docteurs', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);
    $role->syncPermissions([$p1, $p2]);

    Livewire::test(RoleTable::class)
        ->call('openEdit', $role->id)
        ->set('selectedPermissions', [$p2->id])
        ->call('save');

    expect($role->fresh()->permissions->pluck('id')->toArray())
        ->not->toContain($p1->id)
        ->toContain($p2->id);
});

it('la validation n\'exige pas l\'unicité du nom pour le rôle en cours d\'édition', function (): void {
    // Règle : sauvegarder avec son propre nom ne doit pas être rejeté comme doublon.
    $role = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);

    Livewire::test(RoleTable::class)
        ->call('openEdit', $role->id)
        ->set('name', 'infirmier')
        ->call('save')
        ->assertHasNoErrors(['name']);
});

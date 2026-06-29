<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Auth\Http\Livewire\RoleForm;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests du composant Livewire RoleForm.
 *
 * Fixture : le composant permet de créer un rôle et de lui affecter
 * des permissions existantes via des cases à cocher.
 *
 * Règle métier protégée : un rôle doit avoir un nom unique ;
 * les permissions assignées doivent exister en base.
 */

// ─── Création ─────────────────────────────────────────────────────────────────

it('crée un rôle avec un nom valide', function (): void {
    // La création d'un rôle est l'opération fondamentale du module Auth.
    Livewire::test(RoleForm::class)
        ->set('name', 'médecin')
        ->call('save')
        ->assertHasNoErrors();

    expect(Role::where('name', 'médecin')->exists())->toBeTrue();
});

it('rejette un nom de rôle vide', function (): void {
    Livewire::test(RoleForm::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('rejette un nom de rôle déjà utilisé', function (): void {
    Role::create(['name' => 'admin', 'guard_name' => 'web']);

    Livewire::test(RoleForm::class)
        ->set('name', 'admin')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

// ─── Assignation de permissions ───────────────────────────────────────────────

it('assigne les permissions sélectionnées au rôle créé', function (): void {
    // L'objectif métier est de contrôler ce qu'un rôle peut faire dans l'app.
    $p1 = Permission::create(['name' => 'voir patients', 'guard_name' => 'web']);
    $p2 = Permission::create(['name' => 'inscrire patient', 'guard_name' => 'web']);

    Livewire::test(RoleForm::class)
        ->set('name', 'infirmier')
        ->set('selectedPermissions', [$p1->id])
        ->call('save');

    $role = Role::where('name', 'infirmier')->first();

    expect($role->permissions->pluck('id')->toArray())->toContain($p1->id)
        ->and($role->permissions->pluck('id')->toArray())->not->toContain($p2->id);
});

it('réinitialise le formulaire après une création réussie', function (): void {
    Livewire::test(RoleForm::class)
        ->set('name', 'admin')
        ->call('save')
        ->assertSet('name', '');
});

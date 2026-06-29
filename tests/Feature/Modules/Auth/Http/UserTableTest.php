<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Auth\Http\Livewire\UserTable;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests du composant Livewire UserTable.
 *
 * Fixture : le composant liste les utilisateurs avec pagination et recherche.
 * La suppression se fait en ligne avec confirmation (même pattern que RoleTable).
 *
 * Règle métier protégée : un utilisateur supprimé ne doit plus apparaître
 * dans la liste ; la recherche filtre sur le nom et l'email.
 */

// ─── Rendu initial ────────────────────────────────────────────────────────────

it('rend le composant UserTable sans erreur', function (): void {
    Livewire::test(UserTable::class)
        ->assertStatus(200);
});

it('affiche les utilisateurs existants', function (): void {
    User::factory()->create(['name' => 'Moussa Traoré', 'email' => 'moussa@hopital.ml']);
    User::factory()->create(['name' => 'Aminata Coulibaly', 'email' => 'aminata@hopital.ml']);

    Livewire::test(UserTable::class)
        ->assertSee('Moussa Traoré')
        ->assertSee('Aminata Coulibaly');
});

it('affiche un message quand aucun utilisateur n\'existe', function (): void {
    Livewire::test(UserTable::class)
        ->assertSee('Aucun utilisateur');
});

// ─── Recherche ────────────────────────────────────────────────────────────────

it('filtre les utilisateurs par nom', function (): void {
    User::factory()->create(['name' => 'Moussa Traoré']);
    User::factory()->create(['name' => 'Aminata Coulibaly']);

    Livewire::test(UserTable::class)
        ->set('search', 'Moussa')
        ->assertSee('Moussa Traoré')
        ->assertDontSee('Aminata Coulibaly');
});

it('filtre les utilisateurs par email', function (): void {
    User::factory()->create(['name' => 'Moussa Traoré', 'email' => 'moussa@hopital.ml']);
    User::factory()->create(['name' => 'Aminata Coulibaly', 'email' => 'aminata@hopital.ml']);

    Livewire::test(UserTable::class)
        ->set('search', 'aminata@')
        ->assertSee('Aminata Coulibaly')
        ->assertDontSee('Moussa Traoré');
});

// ─── Suppression ──────────────────────────────────────────────────────────────

it('supprime un utilisateur existant', function (): void {
    $user = User::factory()->create(['name' => 'Bakary Diarra']);

    Livewire::test(UserTable::class)
        ->call('delete', $user->id)
        ->assertDontSee('Bakary Diarra');

    expect(User::find($user->id))->toBeNull();
});

// ─── Rôles ────────────────────────────────────────────────────────────────────

it('affiche le rôle associé à chaque utilisateur', function (): void {
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $user = User::factory()->create(['name' => 'Dr Bamba']);
    $user->assignRole($role);

    Livewire::test(UserTable::class)
        ->assertSee('Dr Bamba')
        ->assertSee('médecin');
});

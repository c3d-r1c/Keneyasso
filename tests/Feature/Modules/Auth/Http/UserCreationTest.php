<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests de création d'utilisateur par un admin.
 *
 * Règle métier centrale : l'inscription est réservée aux utilisateurs
 * avec la permission « gérer utilisateurs ». Un admin bypass toutes
 * les permissions via Gate::before().
 */

// ─── Protection de la route ────────────────────────────────────────────────────

it('un invité ne peut pas voir le formulaire de création', function (): void {
    $this->get(route('auth.users.create'))
        ->assertRedirect(route('login'));
});

it('un invité ne peut pas soumettre le formulaire de création', function (): void {
    $this->post(route('auth.users.store'), [])
        ->assertRedirect(route('login'));
});

// ─── Création d'utilisateur ────────────────────────────────────────────────────

it('un utilisateur connecté peut accéder au formulaire de création', function (): void {
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

    $this->actingAs($admin)
        ->get(route('auth.users.create'))
        ->assertOk();
});

it('un admin peut créer un utilisateur et lui assigner un rôle', function (): void {
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);

    $this->actingAs($admin)
        ->post(route('auth.users.store'), [
            'nom' => 'Moussa Konaté',
            'email' => 'moussa@keneyasso.ml',
            'password' => 'motdepasse123',
            'role_id' => $role->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'moussa@keneyasso.ml']);

    $nouvelUtilisateur = User::where('email', 'moussa@keneyasso.ml')->first();
    expect($nouvelUtilisateur->hasRole('médecin'))->toBeTrue();
});

it('l\'email doit être unique', function (): void {
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);
    $role = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);
    User::factory()->create(['email' => 'existant@test.com']);

    $this->actingAs($admin)
        ->post(route('auth.users.store'), [
            'nom' => 'Doublon',
            'email' => 'existant@test.com',
            'password' => 'motdepasse123',
            'role_id' => $role->id,
        ])
        ->assertSessionHasErrors('email');
});

it('le rôle doit exister', function (): void {
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

    $this->actingAs($admin)
        ->post(route('auth.users.store'), [
            'nom' => 'Test',
            'email' => 'test@test.com',
            'password' => 'motdepasse123',
            'role_id' => 9999,
        ])
        ->assertSessionHasErrors('role_id');
});

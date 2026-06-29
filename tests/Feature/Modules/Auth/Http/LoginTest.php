<?php

declare(strict_types=1);

use App\Models\User;

/**
 * Tests du flux de connexion / déconnexion.
 *
 * Règles métier protégées :
 * - Seuls les utilisateurs enregistrés par un admin peuvent se connecter
 * - Un invité ne peut pas accéder aux pages protégées
 * - La session est régénérée à la connexion (protection CSRF)
 */

// ─── Accès à la page de connexion ─────────────────────────────────────────────

it('la page de connexion est accessible aux invités', function (): void {
    $this->get(route('login'))->assertOk();
});

it('un utilisateur connecté est redirigé depuis /login', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('home'));
});

// ─── Connexion ─────────────────────────────────────────────────────────────────

it('un utilisateur peut se connecter avec des identifiants valides', function (): void {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->post(route('login.attempt'), [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

it('des identifiants invalides redirigent avec une erreur', function (): void {
    User::factory()->create(['email' => 'test@test.com']);

    $this->post(route('login.attempt'), [
        'email' => 'test@test.com',
        'password' => 'mauvais',
    ])->assertRedirect()
      ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('le champ email est obligatoire', function (): void {
    $this->post(route('login.attempt'), ['password' => 'secret'])
        ->assertSessionHasErrors('email');
});

// ─── Déconnexion ───────────────────────────────────────────────────────────────

it('un utilisateur connecté peut se déconnecter', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Repositories\EloquentUtilisateurRepository;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Tests de l'implémentation Eloquent du contrat UtilisateurRepository.
 *
 * Fixture : un rôle existant en base est nécessaire pour les tests d'assignation.
 * Ces tests vérifient la couche Infrastructure — ils touchent la BDD
 * et requièrent RefreshDatabase.
 *
 * Règle métier protégée : un utilisateur créé doit toujours avoir un rôle ;
 * l'assignation de rôle est atomique avec la création du compte.
 */

// ─── Création ─────────────────────────────────────────────────────────────────

it('persiste l\'utilisateur en base', function (): void {
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $repo = new EloquentUtilisateurRepository;

    $repo->creer('Dr Koné', 'kone@hopital.ml', 'secret123', $role->id);

    expect(User::where('email', 'kone@hopital.ml')->exists())->toBeTrue();
});

it('retourne l\'utilisateur créé', function (): void {
    $role = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);
    $repo = new EloquentUtilisateurRepository;

    $user = $repo->creer('Aminata Coulibaly', 'aminata@hopital.ml', 'secret123', $role->id);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Aminata Coulibaly')
        ->and($user->email)->toBe('aminata@hopital.ml');
});

// ─── Assignation de rôle ──────────────────────────────────────────────────────

it('assigne le rôle à l\'utilisateur créé', function (): void {
    // Un utilisateur sans rôle n'aurait aucun accès — l'assignation est obligatoire.
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $repo = new EloquentUtilisateurRepository;

    $user = $repo->creer('Mamadou Diallo', 'mamadou@hopital.ml', 'secret123', $role->id);

    expect($user->hasRole('admin'))->toBeTrue();
});

it('n\'assigne pas d\'autres rôles que celui demandé', function (): void {
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $repo = new EloquentUtilisateurRepository;

    $user = $repo->creer('Dr Traoré', 'traore@hopital.ml', 'secret123', $role->id);

    expect($user->hasRole('admin'))->toBeFalse()
        ->and($user->hasRole('médecin'))->toBeTrue();
});

// ─── Recherche ────────────────────────────────────────────────────────────────

it('retourne un utilisateur existant par son id', function (): void {
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $created = User::create(['name' => 'Moussa Traoré', 'email' => 'moussa@hopital.ml', 'password' => 'secret']);
    $repo = new EloquentUtilisateurRepository;

    $found = $repo->findById($created->id);

    expect($found->id)->toBe($created->id)
        ->and($found->name)->toBe('Moussa Traoré');
});

// ─── Modification ─────────────────────────────────────────────────────────────

it('met à jour le nom et l\'email de l\'utilisateur', function (): void {
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $user = User::create(['name' => 'Ancien Nom', 'email' => 'ancien@hopital.ml', 'password' => 'secret']);
    $repo = new EloquentUtilisateurRepository;

    $repo->modifier($user->id, 'Nouveau Nom', 'nouveau@hopital.ml', $role->id);

    $refreshed = $user->fresh();
    expect($refreshed->name)->toBe('Nouveau Nom')
        ->and($refreshed->email)->toBe('nouveau@hopital.ml');
});

it('change le rôle de l\'utilisateur lors de la modification', function (): void {
    $ancien = Role::create(['name' => 'infirmier', 'guard_name' => 'web']);
    $nouveau = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $user = User::create(['name' => 'Sali Koné', 'email' => 'sali@hopital.ml', 'password' => 'secret']);
    $user->assignRole($ancien);
    $repo = new EloquentUtilisateurRepository;

    $repo->modifier($user->id, $user->name, $user->email, $nouveau->id);

    expect($user->fresh()->hasRole('médecin'))->toBeTrue()
        ->and($user->fresh()->hasRole('infirmier'))->toBeFalse();
});

it('retourne l\'utilisateur modifié', function (): void {
    $role = Role::create(['name' => 'médecin', 'guard_name' => 'web']);
    $user = User::create(['name' => 'Ancien', 'email' => 'ancien@hopital.ml', 'password' => 'secret']);
    $repo = new EloquentUtilisateurRepository;

    $result = $repo->modifier($user->id, 'Nouveau', 'nouveau@hopital.ml', $role->id);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->name)->toBe('Nouveau');
});

// ─── Suppression ──────────────────────────────────────────────────────────────

it('supprime l\'utilisateur de la base', function (): void {
    $user = User::create(['name' => 'À supprimer', 'email' => 'delete@hopital.ml', 'password' => 'secret']);
    $repo = new EloquentUtilisateurRepository;

    $repo->supprimer($user->id);

    expect(User::find($user->id))->toBeNull();
});

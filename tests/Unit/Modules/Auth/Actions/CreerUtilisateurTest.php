<?php

declare(strict_types=1);

use App\Models\User;
use Modules\Auth\Actions\CreerUtilisateur;
use Modules\Auth\Domain\UtilisateurRepository;

/**
 * CreerUtilisateur est l'action de création de compte du module Auth.
 *
 * Elle ne doit contenir aucune infrastructure (Eloquent, DB::) —
 * la persistance est déléguée à UtilisateurRepository.
 *
 * Double utilisé :
 * - CreerUtilisateurSpy : enregistre les paramètres reçus, retourne
 *   un User instancié sans passage en base (aucun RefreshDatabase requis).
 */

// ─── Double de test ────────────────────────────────────────────────────────────

/** @internal */
final class CreerUtilisateurSpy implements UtilisateurRepository
{
    public ?string $nomRecu      = null;
    public ?string $emailRecu    = null;
    public ?string $passwordRecu = null;
    public ?int    $roleIdRecu   = null;

    public function creer(string $nom, string $email, string $password, int $roleId): User
    {
        $this->nomRecu      = $nom;
        $this->emailRecu    = $email;
        $this->passwordRecu = $password;
        $this->roleIdRecu   = $roleId;

        return new User(['name' => $nom, 'email' => $email]);
    }

    public function findById(int $id): User { return new User; }

    public function modifier(int $id, string $nom, string $email, int $roleId): User { return new User; }

    public function supprimer(int $id): void {}
}

// ─── Délégation ────────────────────────────────────────────────────────────────

it('délègue la création au repository', function (): void {
    // L'action ne doit pas toucher Eloquent — elle passe par le contrat.
    $spy    = new CreerUtilisateurSpy;
    $action = new CreerUtilisateur($spy);

    $action('Dr Koné', 'kone@hopital.ml', 'secret123', 2);

    expect($spy->nomRecu)->toBe('Dr Koné')
        ->and($spy->emailRecu)->toBe('kone@hopital.ml')
        ->and($spy->passwordRecu)->toBe('secret123')
        ->and($spy->roleIdRecu)->toBe(2);
});

it('retourne l\'utilisateur créé par le repository', function (): void {
    $spy    = new CreerUtilisateurSpy;
    $action = new CreerUtilisateur($spy);

    $user = $action('Aminata', 'aminata@hopital.ml', 'motdepasse', 1);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Aminata');
});

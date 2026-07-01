<?php

declare(strict_types=1);

use App\Models\User;
use Modules\Auth\Actions\ModifierUtilisateur;
use Modules\Auth\Domain\UtilisateurRepository;

/**
 * ModifierUtilisateur délègue la mise à jour au repository.
 *
 * Double utilisé :
 * - ModifierUtilisateurSpy : enregistre l'identifiant et les nouvelles
 *   valeurs reçus, sans aucun accès base de données.
 */

// ─── Double de test ────────────────────────────────────────────────────────────

/** @internal */
final class ModifierUtilisateurSpy implements UtilisateurRepository
{
    public ?int $idRecu = null;

    public ?string $nomRecu = null;

    public ?string $emailRecu = null;

    public ?int $roleIdRecu = null;

    public function creer(string $nom, string $email, string $password, int $roleId): User
    {
        return new User;
    }

    public function findById(int $id): User
    {
        return new User;
    }

    public function modifier(int $id, string $nom, string $email, int $roleId): User
    {
        $this->idRecu = $id;
        $this->nomRecu = $nom;
        $this->emailRecu = $email;
        $this->roleIdRecu = $roleId;

        return new User(['name' => $nom, 'email' => $email]);
    }

    public function supprimer(int $id): void {}
}

// ─── Délégation ────────────────────────────────────────────────────────────────

it('délègue la modification au repository avec les bons paramètres', function (): void {
    // L'action ne contient aucune logique — elle transmet au contrat.
    $spy = new ModifierUtilisateurSpy;
    $action = new ModifierUtilisateur($spy);

    $action(42, 'Dr Koné modifié', 'kone2@hopital.ml', 3);

    expect($spy->idRecu)->toBe(42)
        ->and($spy->nomRecu)->toBe('Dr Koné modifié')
        ->and($spy->emailRecu)->toBe('kone2@hopital.ml')
        ->and($spy->roleIdRecu)->toBe(3);
});

it('retourne l\'utilisateur modifié par le repository', function (): void {
    $spy = new ModifierUtilisateurSpy;
    $action = new ModifierUtilisateur($spy);

    $user = $action(1, 'Nouveau Nom', 'nouveau@hopital.ml', 2);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Nouveau Nom');
});

<?php

declare(strict_types=1);

use App\Models\User;
use Modules\Auth\Actions\SupprimerUtilisateur;
use Modules\Auth\Domain\UtilisateurRepository;

/**
 * SupprimerUtilisateur délègue la suppression au repository.
 *
 * Double utilisé :
 * - SupprimerUtilisateurSpy : enregistre l'identifiant reçu,
 *   sans aucun accès base de données.
 */

// ─── Double de test ────────────────────────────────────────────────────────────

/** @internal */
final class SupprimerUtilisateurSpy implements UtilisateurRepository
{
    public ?int $idRecu = null;

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
        return new User;
    }

    public function supprimer(int $id): void
    {
        $this->idRecu = $id;
    }
}

// ─── Délégation ────────────────────────────────────────────────────────────────

it('délègue la suppression au repository avec le bon identifiant', function (): void {
    // La suppression d'un utilisateur est irréversible — le bon id doit être transmis.
    $spy    = new SupprimerUtilisateurSpy;
    $action = new SupprimerUtilisateur($spy);

    $action(99);

    expect($spy->idRecu)->toBe(99);
});

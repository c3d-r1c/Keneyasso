<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use App\Models\User;
use Modules\Auth\Domain\UtilisateurRepository;

/**
 * Crée un compte utilisateur et lui assigne un rôle.
 *
 * L'inscription n'est jamais publique : cette action n'est appelée
 * que depuis un contexte authentifié (admin).
 * La persistance est déléguée à UtilisateurRepository — zéro Eloquent ici.
 *
 * Exemple :
 *   ($action)('Dr Koné', 'kone@hopital.ml', 'secret123', $roleId);
 */
final class CreerUtilisateur
{
    public function __construct(private readonly UtilisateurRepository $repository) {}

    public function __invoke(string $nom, string $email, string $password, int $roleId): User
    {
        return $this->repository->creer($nom, $email, $password, $roleId);
    }
}

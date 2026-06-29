<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use App\Models\User;
use Modules\Auth\Domain\UtilisateurRepository;

/**
 * Met à jour le nom, l'email et le rôle d'un utilisateur existant.
 *
 * Aucune logique de validation ici — c'est la responsabilité du FormRequest.
 * La persistance est déléguée à UtilisateurRepository.
 */
final class ModifierUtilisateur
{
    public function __construct(private readonly UtilisateurRepository $repository) {}

    public function __invoke(int $id, string $nom, string $email, int $roleId): User
    {
        return $this->repository->modifier($id, $nom, $email, $roleId);
    }
}

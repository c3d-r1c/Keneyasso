<?php

declare(strict_types=1);

namespace Modules\Auth\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Auth\Domain\UtilisateurRepository;
use Spatie\Permission\Models\Role;

/**
 * Implémentation Eloquent du contrat UtilisateurRepository.
 *
 * Responsabilités :
 * - Créer le compte via User::create() (hash du mot de passe géré par le cast)
 * - Assigner le rôle Spatie immédiatement après la création
 *
 * L'assignation de rôle est volontairement couplée à la création :
 * un utilisateur sans rôle n'aurait aucun accès dans l'application.
 */
final class EloquentUtilisateurRepository implements UtilisateurRepository
{
    public function creer(string $nom, string $email, string $password, int $roleId): User
    {
        $user = User::create([
            'name'     => $nom,
            'email'    => $email,
            'password' => $password,
        ]);

        $user->assignRole(Role::findById($roleId));

        return $user;
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function modifier(int $id, string $nom, string $email, int $roleId): User
    {
        $user = User::findOrFail($id);

        $user->update(['name' => $nom, 'email' => $email]);
        $user->syncRoles([Role::findById($roleId)]);

        return $user->fresh();
    }

    public function supprimer(int $id): void
    {
        User::findOrFail($id)->delete();
    }
}

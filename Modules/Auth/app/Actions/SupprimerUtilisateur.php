<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Modules\Auth\Domain\UtilisateurRepository;

/**
 * Supprime définitivement un compte utilisateur.
 *
 * Action irréversible — s'assurer que la confirmation UI a eu lieu
 * avant d'invoquer cette action.
 */
final class SupprimerUtilisateur
{
    public function __construct(private readonly UtilisateurRepository $repository) {}

    public function __invoke(int $id): void
    {
        $this->repository->supprimer($id);
    }
}

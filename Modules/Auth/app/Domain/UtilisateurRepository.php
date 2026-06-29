<?php

declare(strict_types=1);

namespace Modules\Auth\Domain;

use App\Models\User;

/**
 * Contrat de persistance des utilisateurs du module Auth.
 *
 * Le Domain définit ce contrat ; l'Infrastructure l'implémente (Eloquent…).
 * Les Actions (Application) injectent cette interface — jamais Eloquent directement.
 *
 * Usage dans une Action :
 *   $user = $this->repository->creer('Dr Koné', 'kone@hopital.ml', 'secret', $roleId);
 */
interface UtilisateurRepository
{
    /**
     * Crée un compte utilisateur et lui assigne le rôle indiqué.
     */
    public function creer(string $nom, string $email, string $password, int $roleId): User;

    /**
     * Retourne l'utilisateur ou lève une exception s'il n'existe pas.
     */
    public function findById(int $id): User;

    /**
     * Met à jour le nom, l'email et le rôle d'un utilisateur existant.
     */
    public function modifier(int $id, string $nom, string $email, int $roleId): User;

    /**
     * Supprime définitivement le compte utilisateur.
     */
    public function supprimer(int $id): void;
}

<?php

declare(strict_types=1);

namespace App\Core\Domain;

/**
 * Contrat de persistance pour un agrégat.
 *
 * Le Domain définit ce dont il a besoin (l'interface), sans savoir
 * comment c'est stocké. L'Infrastructure fournit l'implémentation concrète.
 *
 * En tests : InMemoryPatientRepository (tableau PHP, rapide, sans BDD).
 * En production : EloquentPatientRepository (MySQL via Eloquent).
 * Le Domain et l'Application ne voient aucune différence.
 *
 * Chaque module définit un contrat spécialisé :
 *   interface PatientRepository extends Repository {
 *       public function findByNumeroSecuriteSociale(string $numero): ?Patient;
 *   }
 */
interface Repository
{
    /** Persiste un agrégat (création ou mise à jour). */
    public function save(AggregateRoot $entity): void;

    /** Retrouve un agrégat par son identifiant, ou null s'il n'existe pas. */
    public function findById(EntityId $id): ?AggregateRoot;

    /**
     * Génère le prochain identifiant disponible.
     * À appeler avant de construire un nouvel agrégat :
     *   $id = $repository->nextId();
     *   $patient = Patient::inscrire($id, $nom);
     */
    public function nextId(): EntityId;
}

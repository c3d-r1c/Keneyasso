<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

/**
 * Contrat que toute implémentation de persistance des médecins doit respecter.
 *
 * Le Domain définit ce contrat ; l'Infrastructure l'implémente (Eloquent, PDO…).
 * Cela préserve la règle de dépendance : le Domain ne connaît pas Laravel.
 *
 * Usage dans un Handler :
 *   $id      = $this->repository->nextId();
 *   $docteur = Docteur::inscrire($id, $nom, $specialite, $numeroOrdre);
 *   $this->repository->save($docteur);
 */
interface DocteurRepository
{
    /**
     * Persiste un médecin (création ou mise à jour par id).
     */
    public function save(Docteur $docteur): void;

    /**
     * Retourne le médecin ou null s'il n'existe pas.
     * Utiliser getById() quand l'absence est une erreur métier.
     */
    public function findById(DocteurId $id): ?Docteur;

    /**
     * Retourne le médecin ou lève DocteurIntrouvable.
     */
    public function getById(DocteurId $id): Docteur;

    /**
     * Génère un identifiant unique avant la création.
     */
    public function nextId(): DocteurId;
}

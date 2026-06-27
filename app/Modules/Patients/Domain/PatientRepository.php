<?php

declare(strict_types=1);

namespace App\Modules\Patients\Domain;

/**
 * Contrat que toute implémentation de persistance des patients doit respecter.
 *
 * Le Domain définit ce contrat ; l'Infrastructure l'implémente (Eloquent, PDO…).
 * Cela préserve la règle de dépendance : le Domain ne connaît pas Laravel.
 *
 * Usage dans un Handler :
 *   $id      = $this->repository->nextId();
 *   $patient = Patient::inscrire($id, $nom, $dateDeNaissance);
 *   $this->repository->save($patient);
 */
interface PatientRepository
{
    /**
     * Persiste un patient (création ou mise à jour par id).
     */
    public function save(Patient $patient): void;

    /**
     * Retourne le patient ou null s'il n'existe pas.
     * Utiliser getById() quand l'absence est une erreur métier.
     */
    public function findById(PatientId $id): ?Patient;

    /**
     * Retourne le patient ou lève PatientIntrouvable.
     */
    public function getById(PatientId $id): Patient;

    /**
     * Génère un identifiant unique avant la création.
     */
    public function nextId(): PatientId;
}

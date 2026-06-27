<?php

declare(strict_types=1);

namespace Modules\Patients\Domain;

use App\Core\Domain\AggregateRoot;

/**
 * Agrégat central du module Patients.
 *
 * Patient est le point d'entrée unique pour toute modification :
 * on ne modifie jamais ses propriétés directement depuis l'extérieur.
 * Toute action métier passe par une méthode nommée d'après l'intention
 * (inscrire, mettreAJour, archiver…) qui enregistre l'événement correspondant.
 *
 * Deux fabriques statiques :
 *   inscrire()     → nouveau patient, émet PatientInscrit
 *   reconstituer() → chargement depuis la persistence, aucun événement
 */
final class Patient extends AggregateRoot
{
    private function __construct(
        private readonly PatientId $id,
        private readonly Nom $nom,
        private readonly DateDeNaissance $dateDeNaissance,
    ) {}

    /**
     * Inscrit un nouveau patient et émet PatientInscrit.
     * C'est le seul moyen de créer un Patient — pas de new Patient() direct.
     */
    public static function inscrire(
        PatientId $id,
        Nom $nom,
        DateDeNaissance $dateDeNaissance,
    ): self {
        $patient = new self($id, $nom, $dateDeNaissance);
        $patient->record(new PatientInscrit($id->value(), (string) $nom));

        return $patient;
    }

    /**
     * Recrée un Patient depuis la persistence sans réémettre d'événements.
     * Utilisé exclusivement par EloquentPatientRepository::toDomain().
     */
    public static function reconstituer(
        PatientId $id,
        Nom $nom,
        DateDeNaissance $dateDeNaissance,
    ): self {
        return new self($id, $nom, $dateDeNaissance);
    }

    public function id(): PatientId
    {
        return $this->id;
    }

    public function nom(): Nom
    {
        return $this->nom;
    }

    public function dateDeNaissance(): DateDeNaissance
    {
        return $this->dateDeNaissance;
    }
}

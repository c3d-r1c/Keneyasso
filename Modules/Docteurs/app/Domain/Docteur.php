<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\AggregateRoot;

/**
 * Agrégat central du module Docteurs.
 *
 * Docteur est le point d'entrée unique pour toute modification :
 * on ne modifie jamais ses propriétés directement depuis l'extérieur.
 * Toute action métier passe par une méthode nommée d'après l'intention
 * (inscrire, mettreAJour, archiver…) qui enregistre l'événement correspondant.
 *
 * Deux fabriques statiques :
 *   inscrire()     → nouveau médecin, émet DocteurInscrit
 *   reconstituer() → chargement depuis la persistence, aucun événement
 */
final class Docteur extends AggregateRoot
{
    private function __construct(
        private readonly DocteurId $id,
        private readonly Nom $nom,
        private readonly Specialite $specialite,
        private readonly NumeroOrdre $numeroOrdre,
    ) {}

    /**
     * Inscrit un nouveau médecin et émet DocteurInscrit.
     * C'est le seul moyen de créer un Docteur — pas de new Docteur() direct.
     */
    public static function inscrire(
        DocteurId $id,
        Nom $nom,
        Specialite $specialite,
        NumeroOrdre $numeroOrdre,
    ): self {
        $docteur = new self($id, $nom, $specialite, $numeroOrdre);
        $docteur->record(new DocteurInscrit($id->value(), (string) $nom, (string) $specialite));

        return $docteur;
    }

    /**
     * Recrée un Docteur depuis la persistence sans réémettre d'événements.
     * Utilisé exclusivement par EloquentDocteurRepository::toDomain().
     */
    public static function reconstituer(
        DocteurId $id,
        Nom $nom,
        Specialite $specialite,
        NumeroOrdre $numeroOrdre,
    ): self {
        return new self($id, $nom, $specialite, $numeroOrdre);
    }

    public function id(): DocteurId
    {
        return $this->id;
    }

    public function nom(): Nom
    {
        return $this->nom;
    }

    public function specialite(): Specialite
    {
        return $this->specialite;
    }

    public function numeroOrdre(): NumeroOrdre
    {
        return $this->numeroOrdre;
    }
}

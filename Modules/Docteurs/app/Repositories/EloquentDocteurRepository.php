<?php

declare(strict_types=1);

namespace Modules\Docteurs\Repositories;

use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurIntrouvable;
use Modules\Docteurs\Domain\DocteurRepository;
use Modules\Docteurs\Domain\Nom;
use Modules\Docteurs\Domain\NumeroOrdre;
use Modules\Docteurs\Domain\Specialite;
use Modules\Docteurs\Models\DocteurModel;

/**
 * Implémentation Eloquent du contrat DocteurRepository.
 *
 * Responsabilités :
 * - Traduire un Docteur (Domain) en colonnes SQL via DocteurModel
 * - Reconstruire un Docteur depuis DocteurModel via Docteur::reconstituer()
 *
 * On utilise updateOrCreate() pour que save() soit idempotent :
 * le même médecin peut être sauvegardé plusieurs fois sans créer de doublon.
 */
final class EloquentDocteurRepository implements DocteurRepository
{
    public function save(Docteur $docteur): void
    {
        DocteurModel::updateOrCreate(
            ['id' => $docteur->id()->value()],
            [
                'prenom' => $docteur->nom()->prenom(),
                'nom_de_famille' => $docteur->nom()->nomDeFamille(),
                'specialite' => $docteur->specialite()->valeur(),
                'numero_ordre' => $docteur->numeroOrdre()->valeur(),
            ],
        );
    }

    public function findById(DocteurId $id): ?Docteur
    {
        $model = DocteurModel::find($id->value());

        return $model instanceof DocteurModel ? $this->toDomain($model) : null;
    }

    public function getById(DocteurId $id): Docteur
    {
        return $this->findById($id) ?? throw DocteurIntrouvable::avecId($id);
    }

    public function nextId(): DocteurId
    {
        return DocteurId::generate();
    }

    /**
     * Reconstruit un Docteur depuis un DocteurModel sans émettre d'événements.
     * On utilise reconstituer() et non inscrire() — les événements ont déjà eu lieu.
     */
    private function toDomain(DocteurModel $model): Docteur
    {
        return Docteur::reconstituer(
            DocteurId::fromString($model->id),
            new Nom($model->prenom, $model->nom_de_famille),
            new Specialite($model->specialite),
            new NumeroOrdre($model->numero_ordre),
        );
    }
}

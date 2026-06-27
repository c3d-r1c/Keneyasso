<?php

declare(strict_types=1);

namespace Modules\Patients\Repositories;

use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Nom;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientIntrouvable;
use Modules\Patients\Domain\PatientRepository;
use Modules\Patients\Models\PatientModel;

/**
 * Implémentation Eloquent du contrat PatientRepository.
 *
 * Responsabilités :
 * - Traduire un Patient (Domain) en colonnes SQL via PatientModel
 * - Reconstruire un Patient depuis PatientModel via Patient::reconstituer()
 *
 * On utilise updateOrCreate() pour que save() soit idempotent :
 * le même patient peut être sauvegardé plusieurs fois sans créer de doublon.
 */
final class EloquentPatientRepository implements PatientRepository
{
    public function save(Patient $patient): void
    {
        PatientModel::updateOrCreate(
            ['id' => $patient->id()->value()],
            [
                'prenom' => $patient->nom()->prenom(),
                'nom_de_famille' => $patient->nom()->nomDeFamille(),
                'date_de_naissance' => $patient->dateDeNaissance()->valeur()->format('Y-m-d'),
            ],
        );
    }

    public function findById(PatientId $id): ?Patient
    {
        $model = PatientModel::find($id->value());

        return $model instanceof PatientModel ? $this->toDomain($model) : null;
    }

    public function getById(PatientId $id): Patient
    {
        return $this->findById($id) ?? throw PatientIntrouvable::avecId($id);
    }

    public function nextId(): PatientId
    {
        return PatientId::generate();
    }

    /**
     * Reconstruit un Patient depuis un PatientModel sans émettre d'événements.
     * On utilise reconstituer() et non inscrire() — les événements ont déjà eu lieu.
     */
    private function toDomain(PatientModel $model): Patient
    {
        return Patient::reconstituer(
            PatientId::fromString($model->id),
            new Nom($model->prenom, $model->nom_de_famille),
            DateDeNaissance::fromString($model->date_de_naissance),
        );
    }
}

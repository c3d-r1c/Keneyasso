<?php

declare(strict_types=1);

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Eloquent représentant la table patients.
 *
 * Ce modèle est PRIVÉ au module Patients — aucun autre module ne doit
 * l'importer directement. La communication inter-modules passe par
 * l'interface PatientRepository et les événements Domain.
 *
 * Il n'expose pas de logique métier : c'est un pur outil de persistance.
 *
 * @property string $id
 * @property string $prenom
 * @property string $nom_de_famille
 * @property string $date_de_naissance
 */
final class PatientModel extends Model
{
    protected $table = 'patients';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'prenom',
        'nom_de_famille',
        'date_de_naissance',
    ];
}

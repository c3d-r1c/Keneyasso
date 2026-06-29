<?php

declare(strict_types=1);

namespace Modules\Docteurs\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Eloquent représentant la table docteurs.
 *
 * Ce modèle est PRIVÉ au module Docteurs — aucun autre module ne doit
 * l'importer directement. La communication inter-modules passe par
 * l'interface DocteurRepository et les événements Domain.
 *
 * Il n'expose pas de logique métier : c'est un pur outil de persistance.
 *
 * @property string $id
 * @property string $prenom
 * @property string $nom_de_famille
 * @property string $specialite
 * @property string $numero_ordre
 */
final class DocteurModel extends Model
{
    protected $table = 'docteurs';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'prenom',
        'nom_de_famille',
        'specialite',
        'numero_ordre',
    ];
}

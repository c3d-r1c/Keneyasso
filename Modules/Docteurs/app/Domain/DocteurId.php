<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\EntityId;

/**
 * Identifiant unique d'un Docteur.
 *
 * Sous-type de EntityId — garantit qu'un DocteurId ne peut jamais
 * être passé là où un PatientId ou ConsultationId est attendu.
 *
 * Usage :
 *   $id = DocteurId::generate();           // nouveau médecin
 *   $id = DocteurId::fromString($uuidBdd); // reconstruction depuis la BDD
 */
final class DocteurId extends EntityId {}

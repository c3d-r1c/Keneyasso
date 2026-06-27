<?php

declare(strict_types=1);

namespace Modules\Patients\Domain;

use App\Core\Domain\EntityId;

/**
 * Identifiant unique d'un Patient.
 *
 * Sous-type de EntityId — garantit qu'un PatientId ne peut jamais
 * être passé là où un DoctorId ou ConsultationId est attendu.
 *
 * Usage :
 *   $id = PatientId::generate();           // nouveau patient
 *   $id = PatientId::fromString($uuidBdd); // reconstruction depuis la BDD
 */
final class PatientId extends EntityId {}

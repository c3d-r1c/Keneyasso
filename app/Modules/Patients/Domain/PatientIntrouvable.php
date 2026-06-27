<?php

declare(strict_types=1);

namespace App\Modules\Patients\Domain;

use App\Core\Domain\DomainException;

/**
 * Levée quand un Patient ne peut pas être trouvé par son identifiant.
 *
 * La couche Presentation attrape DomainException (type parent) pour
 * renvoyer un HTTP 404 sans connaître PatientIntrouvable spécifiquement —
 * ce qui préserve le découplage entre Presentation et Domain.
 *
 * Usage :
 *   $patient = $this->repository->findById($id)
 *       ?? throw PatientIntrouvable::avecId($id);
 */
final class PatientIntrouvable extends DomainException
{
    public static function avecId(PatientId $id): self
    {
        return new self("Patient introuvable : {$id}");
    }
}

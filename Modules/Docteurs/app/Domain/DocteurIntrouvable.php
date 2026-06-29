<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\DomainException;

/**
 * Levée quand un Docteur ne peut pas être trouvé par son identifiant.
 *
 * La couche Presentation attrape DomainException (type parent) pour
 * renvoyer un HTTP 404 sans connaître DocteurIntrouvable spécifiquement —
 * ce qui préserve le découplage entre Presentation et Domain.
 *
 * Usage :
 *   $docteur = $this->repository->findById($id)
 *       ?? throw DocteurIntrouvable::avecId($id);
 */
final class DocteurIntrouvable extends DomainException
{
    public static function avecId(DocteurId $id): self
    {
        return new self("Docteur introuvable : {$id}");
    }
}

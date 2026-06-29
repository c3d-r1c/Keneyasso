<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\ValueObject;
use InvalidArgumentException;

/**
 * Numéro d'ordre du Conseil de l'Ordre des Médecins.
 *
 * C'est l'identifiant officiel prouvant qu'un médecin est autorisé à exercer.
 * Le format varie selon les pays (alphanumérique, avec tirets, etc.) — le Domain
 * vérifie uniquement qu'il n'est pas vide et supprime les espaces superflus.
 * La validation du format exact relève de la couche Présentation.
 */
final class NumeroOrdre extends ValueObject implements \Stringable
{
    private readonly string $valeur;

    public function __construct(string $valeur)
    {
        if (trim($valeur) === '') {
            throw new InvalidArgumentException("Le numéro d'ordre ne peut pas être vide");
        }

        $this->valeur = trim($valeur);
    }

    public function valeur(): string
    {
        return $this->valeur;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->valeur === $other->valeur;
    }

    public function __toString(): string
    {
        return $this->valeur;
    }
}

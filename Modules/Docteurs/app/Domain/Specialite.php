<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\ValueObject;
use InvalidArgumentException;

/**
 * Discipline médicale d'un docteur (ex. Cardiologie, Pédiatrie, Médecine générale).
 *
 * Le Domain garantit qu'une spécialité ne peut pas être vide et est normalisée
 * en Ucfirst pour l'affichage cohérent dans toute l'application.
 * Toute validation de liste blanche (spécialités reconnues) est volontairement
 * absente ici : elle appartient à la couche Présentation ou à un service dédié.
 */
final class Specialite extends ValueObject implements \Stringable
{
    private readonly string $valeur;

    public function __construct(string $valeur)
    {
        if (trim($valeur) === '') {
            throw new InvalidArgumentException('La spécialité ne peut pas être vide');
        }

        $this->valeur = mb_ucfirst(mb_strtolower(trim($valeur)));
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

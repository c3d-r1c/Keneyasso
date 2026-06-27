<?php

declare(strict_types=1);

namespace App\Modules\Patients\Domain;

use App\Core\Domain\ValueObject;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Date de naissance d'un patient.
 *
 * Encapsule les règles de validation métier :
 * - Format ISO 8601 (Y-m-d) obligatoire
 * - Pas de date future (un patient ne peut pas être né demain)
 * - Pas avant 1900 (borne raisonnable pour un système actif)
 *
 * L'âge est calculé dynamiquement pour rester toujours exact,
 * sans risque de désynchronisation avec la date réelle.
 */
final class DateDeNaissance extends ValueObject
{
    private function __construct(private readonly DateTimeImmutable $valeur) {}

    public static function fromString(string $date): self
    {
        $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);

        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException("Format de date invalide : {$date}. Format attendu : Y-m-d");
        }

        if ($parsed > new DateTimeImmutable('today')) {
            throw new InvalidArgumentException('La date de naissance ne peut pas être dans le futur');
        }

        if ($parsed->format('Y') < '1900') {
            throw new InvalidArgumentException('La date de naissance ne peut pas être antérieure à 1900');
        }

        return new self($parsed);
    }

    public function valeur(): DateTimeImmutable
    {
        return $this->valeur;
    }

    /** Calcule l'âge en années révolues à la date d'aujourd'hui. */
    public function age(): int
    {
        return (int) $this->valeur->diff(new DateTimeImmutable('today'))->y;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->valeur->format('Y-m-d') === $other->valeur->format('Y-m-d');
    }
}

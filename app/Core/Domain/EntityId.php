<?php

declare(strict_types=1);

namespace App\Core\Domain;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Identifiant unique typé basé sur un UUID v4.
 *
 * Chaque module définit son propre type en étendant cette classe :
 *   class PatientId extends EntityId {}
 *   class DoctorId  extends EntityId {}
 *
 * Le typage fort empêche de passer un DoctorId là où un PatientId est attendu —
 * une erreur que PHP plain string laisserait passer silencieusement.
 *
 * @phpstan-consistent-constructor
 */
abstract class EntityId implements \Stringable
{
    protected function __construct(private readonly string $value) {}

    /**
     * Reconstruit un EntityId depuis une chaîne UUID existante (lecture BDD, API…).
     *
     * @throws InvalidArgumentException si la valeur n'est pas un UUID valide.
     */
    public static function fromString(string $value): static
    {
        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            throw new InvalidArgumentException("UUID invalide : {$value}");
        }

        return new static($value);
    }

    /**
     * Génère un nouvel identifiant unique.
     * Utilisé par le Repository (nextId()) avant de créer un agrégat.
     */
    public static function generate(): static
    {
        return new static((string) Str::uuid());
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * Égalité structurelle : deux EntityId du même type avec le même UUID sont identiques.
     * Des sous-types différents (PatientId vs DoctorId) ne peuvent pas être comparés.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

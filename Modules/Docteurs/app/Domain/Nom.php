<?php

declare(strict_types=1);

namespace Modules\Docteurs\Domain;

use App\Core\Domain\ValueObject;
use InvalidArgumentException;

/**
 * Nom complet d'un docteur : prénom + nom de famille.
 *
 * Encapsule les règles de normalisation et de validation métier :
 * - Prénom en Ucfirst (ex. "ibrahim" → "Ibrahim")
 * - Nom de famille en MAJUSCULES (ex. "coulibaly" → "COULIBALY")
 * - Aucune valeur vide autorisée
 *
 * Ces règles sont portées par le Domain, pas par un FormRequest Laravel,
 * ce qui garantit leur respect quelle que soit l'entrée (API, CLI, Livewire…).
 */
final class Nom extends ValueObject implements \Stringable
{
    private readonly string $prenom;

    private readonly string $nomDeFamille;

    public function __construct(string $prenom, string $nomDeFamille)
    {
        if (trim($prenom) === '') {
            throw new InvalidArgumentException('Le prénom ne peut pas être vide');
        }

        if (trim($nomDeFamille) === '') {
            throw new InvalidArgumentException('Le nom de famille ne peut pas être vide');
        }

        $this->prenom = mb_ucfirst(mb_strtolower(trim($prenom)));
        $this->nomDeFamille = mb_strtoupper(trim($nomDeFamille));
    }

    public function prenom(): string
    {
        return $this->prenom;
    }

    public function nomDeFamille(): string
    {
        return $this->nomDeFamille;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->prenom === $other->prenom
            && $this->nomDeFamille === $other->nomDeFamille;
    }

    public function __toString(): string
    {
        return "{$this->prenom} {$this->nomDeFamille}";
    }
}

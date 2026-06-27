<?php

declare(strict_types=1);

use App\Modules\Patients\Domain\DateDeNaissance;

/**
 * DateDeNaissance encapsule la date de naissance du patient
 * et ses règles métier : pas de date future, pas avant 1900,
 * et calcul de l'âge toujours cohérent avec la date du jour.
 */

// ─── Construction ─────────────────────────────────────────────────────────────

it('crée une DateDeNaissance valide', function (): void {
    $date = DateDeNaissance::fromString('1990-05-15');

    expect($date->valeur()->format('Y-m-d'))->toBe('1990-05-15');
});

// ─── Validation ───────────────────────────────────────────────────────────────

it('rejette une date dans le futur', function (): void {
    // Un patient ne peut pas être né dans le futur — invariant métier absolu.
    $demain = (new DateTimeImmutable('+1 day'))->format('Y-m-d');

    DateDeNaissance::fromString($demain);
})->throws(InvalidArgumentException::class, 'La date de naissance ne peut pas être dans le futur');

it('rejette une date antérieure à 1900', function (): void {
    // Borne inférieure raisonnable pour un système médical actif.
    DateDeNaissance::fromString('1899-12-31');
})->throws(InvalidArgumentException::class, 'La date de naissance ne peut pas être antérieure à 1900');

it('rejette un format de date invalide', function (): void {
    DateDeNaissance::fromString('pas-une-date');
})->throws(InvalidArgumentException::class);

// ─── Âge ──────────────────────────────────────────────────────────────────────

it('calcule l\'âge du patient en années', function (): void {
    // L'âge est recalculé dynamiquement — pas stocké pour éviter la désynchronisation.
    $date = DateDeNaissance::fromString('1990-01-01');

    expect($date->age())->toBeGreaterThanOrEqual(35);
});

// ─── Égalité ──────────────────────────────────────────────────────────────────

it('deux DateDeNaissance identiques sont égales', function (): void {
    $a = DateDeNaissance::fromString('1990-05-15');
    $b = DateDeNaissance::fromString('1990-05-15');

    expect($a->equals($b))->toBeTrue();
});

it('deux DateDeNaissance différentes ne sont pas égales', function (): void {
    $a = DateDeNaissance::fromString('1990-05-15');
    $b = DateDeNaissance::fromString('1985-03-20');

    expect($a->equals($b))->toBeFalse();
});

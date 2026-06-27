<?php

declare(strict_types=1);

use App\Core\Domain\ValueObject;
use App\Modules\Patients\Domain\Nom;

/**
 * Nom est un ValueObject qui encapsule prénom + nom de famille du patient.
 * Il normalise la casse et rejette les valeurs vides —
 * règles métier portées par le Domain, pas par un FormRequest Laravel.
 */

// ─── Construction ─────────────────────────────────────────────────────────────

it('crée un Nom valide', function (): void {
    // La normalisation s'applique immédiatement à la construction.
    $nom = new Nom('Moussa', 'Traoré');

    expect($nom->prenom())->toBe('Moussa')
        ->and($nom->nomDeFamille())->toBe('TRAORÉ');
});

it('normalise la casse en majuscule pour le nom de famille', function (): void {
    // Invariant métier : le nom de famille est toujours en majuscules dans le dossier médical.
    $nom = new Nom('Moussa', 'traoré');

    expect($nom->nomDeFamille())->toBe('TRAORÉ');
});

it('normalise la casse en ucfirst pour le prénom', function (): void {
    $nom = new Nom('moussa', 'Traoré');

    expect($nom->prenom())->toBe('Moussa');
});

// ─── Validation ───────────────────────────────────────────────────────────────

it('rejette un prénom vide', function (): void {
    // Un patient sans prénom ne peut pas être inscrit — règle métier fondamentale.
    new Nom('', 'Traoré');
})->throws(InvalidArgumentException::class, 'Le prénom ne peut pas être vide');

it('rejette un nom de famille vide', function (): void {
    new Nom('Moussa', '');
})->throws(InvalidArgumentException::class, 'Le nom de famille ne peut pas être vide');

// ─── Égalité ──────────────────────────────────────────────────────────────────

it('deux Nom identiques sont égaux', function (): void {
    $a = new Nom('Moussa', 'Traoré');
    $b = new Nom('Moussa', 'Traoré');

    expect($a->equals($b))->toBeTrue();
});

it('deux Nom différents ne sont pas égaux', function (): void {
    $a = new Nom('Moussa', 'Traoré');
    $b = new Nom('Amadou', 'Diallo');

    expect($a->equals($b))->toBeFalse();
});

// ─── Affichage ────────────────────────────────────────────────────────────────

it('se convertit en string pour les logs et l\'affichage', function (): void {
    $nom = new Nom('Moussa', 'Traoré');

    expect((string) $nom)->toBe('Moussa TRAORÉ');
});

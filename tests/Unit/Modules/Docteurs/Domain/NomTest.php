<?php

declare(strict_types=1);

use Modules\Docteurs\Domain\Nom;

/**
 * Nom est un ValueObject représentant le nom complet d'un docteur.
 * Règles de normalisation : prénom en Ucfirst, nom de famille en MAJUSCULES.
 * Ces règles sont identiques à celles du module Patients — chaque module
 * possède sa propre copie pour rester indépendant.
 */

// ─── Validation ───────────────────────────────────────────────────────────────

it('accepte un prénom et un nom de famille valides', function (): void {
    $nom = new Nom('Ibrahim', 'Coulibaly');
    expect($nom->prenom())->toBe('Ibrahim')
        ->and($nom->nomDeFamille())->toBe('COULIBALY');
});

it('rejette un prénom vide', function (): void {
    expect(fn () => new Nom('', 'Coulibaly'))->toThrow(InvalidArgumentException::class);
});

it('rejette un nom de famille vide', function (): void {
    expect(fn () => new Nom('Ibrahim', ''))->toThrow(InvalidArgumentException::class);
});

it('rejette un prénom composé uniquement d\'espaces', function (): void {
    expect(fn () => new Nom('   ', 'Coulibaly'))->toThrow(InvalidArgumentException::class);
});

// ─── Normalisation ────────────────────────────────────────────────────────────

it('normalise le prénom en Ucfirst', function (): void {
    expect((new Nom('ibRahIM', 'Coulibaly'))->prenom())->toBe('Ibrahim');
});

it('normalise le nom de famille en majuscules', function (): void {
    expect((new Nom('Ibrahim', 'coulibaly'))->nomDeFamille())->toBe('COULIBALY');
});

it('supprime les espaces superflus', function (): void {
    $nom = new Nom('  Ibrahim  ', '  Coulibaly  ');
    expect($nom->prenom())->toBe('Ibrahim')
        ->and($nom->nomDeFamille())->toBe('COULIBALY');
});

// ─── Représentation ───────────────────────────────────────────────────────────

it('se représente sous la forme "Prénom NOM"', function (): void {
    expect((string) new Nom('Ibrahim', 'Coulibaly'))->toBe('Ibrahim COULIBALY');
});

// ─── Égalité ──────────────────────────────────────────────────────────────────

it('deux Nom identiques sont égaux', function (): void {
    expect((new Nom('Ibrahim', 'Coulibaly'))->equals(new Nom('ibrahim', 'coulibaly')))->toBeTrue();
});

it('deux Nom différents ne sont pas égaux', function (): void {
    expect((new Nom('Ibrahim', 'Coulibaly'))->equals(new Nom('Moussa', 'Traoré')))->toBeFalse();
});

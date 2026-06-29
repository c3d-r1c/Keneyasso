<?php

declare(strict_types=1);

use Modules\Docteurs\Domain\Specialite;

/**
 * Specialite représente la discipline médicale d'un docteur (ex. Cardiologie, Pédiatrie).
 * Le Domain garantit qu'une spécialité ne peut jamais être vide
 * et qu'elle est normalisée en Ucfirst pour l'affichage cohérent.
 */

// ─── Validation ───────────────────────────────────────────────────────────────

it('accepte une spécialité valide', function (): void {
    $specialite = new Specialite('Cardiologie');
    expect($specialite->valeur())->toBe('Cardiologie');
});

it('rejette une spécialité vide', function (): void {
    expect(fn (): \Modules\Docteurs\Domain\Specialite => new Specialite(''))->toThrow(InvalidArgumentException::class);
});

it('rejette une spécialité composée uniquement d\'espaces', function (): void {
    expect(fn (): \Modules\Docteurs\Domain\Specialite => new Specialite('   '))->toThrow(InvalidArgumentException::class);
});

// ─── Normalisation ────────────────────────────────────────────────────────────

it('normalise en Ucfirst', function (): void {
    expect((new Specialite('cardiologie'))->valeur())->toBe('Cardiologie');
});

it('supprime les espaces superflus', function (): void {
    expect((new Specialite('  Pédiatrie  '))->valeur())->toBe('Pédiatrie');
});

// ─── Égalité ──────────────────────────────────────────────────────────────────

it('deux Specialite identiques sont égales', function (): void {
    expect((new Specialite('Cardiologie'))->equals(new Specialite('cardiologie')))->toBeTrue();
});

it('deux Specialite différentes ne sont pas égales', function (): void {
    expect((new Specialite('Cardiologie'))->equals(new Specialite('Pédiatrie')))->toBeFalse();
});

// ─── Représentation ───────────────────────────────────────────────────────────

it('se représente comme sa valeur normalisée', function (): void {
    expect((string) new Specialite('cardiologie'))->toBe('Cardiologie');
});

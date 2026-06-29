<?php

declare(strict_types=1);

use App\Core\Domain\EntityId;
use Modules\Docteurs\Domain\DocteurId;

/**
 * DocteurId est l'identifiant unique d'un Docteur.
 * Sous-type de EntityId — garantit le typage fort entre les identifiants
 * de différents agrégats (un DocteurId ne peut pas circuler comme PatientId).
 */

// ─── Construction ─────────────────────────────────────────────────────────────

it('DocteurId est un EntityId', function (): void {
    expect(DocteurId::generate())->toBeInstanceOf(EntityId::class);
});

it('génère deux identifiants différents', function (): void {
    expect(DocteurId::generate()->value())->not->toBe(DocteurId::generate()->value());
});

it('se reconstruit depuis un UUID valide', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    expect(DocteurId::fromString($uuid)->value())->toBe($uuid);
});

it('rejette une chaîne qui n\'est pas un UUID', function (): void {
    expect(fn (): \Modules\Docteurs\Domain\DocteurId => DocteurId::fromString('pas-un-uuid'))->toThrow(InvalidArgumentException::class);
});

// ─── Égalité ──────────────────────────────────────────────────────────────────

it('deux DocteurId avec le même UUID sont égaux', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    expect(DocteurId::fromString($uuid)->equals(DocteurId::fromString($uuid)))->toBeTrue();
});

it('deux DocteurId différents ne sont pas égaux', function (): void {
    expect(DocteurId::generate()->equals(DocteurId::generate()))->toBeFalse();
});

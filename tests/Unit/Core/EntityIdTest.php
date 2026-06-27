<?php

declare(strict_types=1);

use App\Core\Domain\EntityId;

/**
 * EntityId est abstraite — chaque module définit son propre type concret.
 * TestEntityId simule ce que ferait PatientId, DoctorId, etc. dans les vrais modules.
 */
class TestEntityId extends EntityId {}

// ─── Construction ────────────────────────────────────────────────────────────

it('crée un EntityId à partir d\'un UUID valide', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $id = TestEntityId::fromString($uuid);

    expect($id->value())->toBe($uuid);
});

it('génère un EntityId unique', function (): void {
    // Deux appels successifs à generate() ne produisent jamais le même UUID.
    $a = TestEntityId::generate();
    $b = TestEntityId::generate();

    expect($a->equals($b))->toBeFalse();
});

// ─── Égalité ─────────────────────────────────────────────────────────────────

it('deux EntityId avec le même UUID sont égaux', function (): void {
    // Garantit qu'on peut retrouver un agrégat en mémoire ou comparer des IDs.
    $uuid = '550e8400-e29b-41d4-a716-446655440000';

    expect(TestEntityId::fromString($uuid)->equals(TestEntityId::fromString($uuid)))->toBeTrue();
});

// ─── Conversion ──────────────────────────────────────────────────────────────

it('se convertit en string', function (): void {
    // Nécessaire pour l'interpolation dans les messages d'erreur et les logs.
    $uuid = '550e8400-e29b-41d4-a716-446655440000';

    expect((string) TestEntityId::fromString($uuid))->toBe($uuid);
});

// ─── Validation ──────────────────────────────────────────────────────────────

it('lève une exception pour un UUID invalide', function (): void {
    // Protège contre les IDs corrompus venant de l'extérieur (API, form…).
    TestEntityId::fromString('pas-un-uuid');
})->throws(InvalidArgumentException::class);

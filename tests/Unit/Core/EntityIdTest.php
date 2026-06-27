<?php

declare(strict_types=1);

use App\Core\Domain\EntityId;

// Sous-classe concrète de test (chaque module aura la sienne : PatientId, DoctorId…)
class TestEntityId extends EntityId {}

it('crée un EntityId à partir d\'un UUID valide', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $id = TestEntityId::fromString($uuid);

    expect($id->value())->toBe($uuid);
});

it('génère un EntityId unique', function (): void {
    $a = TestEntityId::generate();
    $b = TestEntityId::generate();

    expect($a->equals($b))->toBeFalse();
});

it('deux EntityId avec le même UUID sont égaux', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';

    expect(TestEntityId::fromString($uuid)->equals(TestEntityId::fromString($uuid)))->toBeTrue();
});

it('se convertit en string', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';

    expect((string) TestEntityId::fromString($uuid))->toBe($uuid);
});

it('lève une exception pour un UUID invalide', function (): void {
    TestEntityId::fromString('pas-un-uuid');
})->throws(InvalidArgumentException::class);

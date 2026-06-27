<?php

declare(strict_types=1);

use App\Core\Domain\EntityId;
use Modules\Patients\Domain\PatientId;

/**
 * PatientId est le type d'identifiant propre au module Patients.
 * Étendre EntityId garantit qu'un PatientId ne peut jamais être confondu
 * avec un DoctorId ou un ConsultationId — même UUID, types incompatibles.
 */

// ─── Héritage ─────────────────────────────────────────────────────────────────

it('PatientId est un EntityId', function (): void {
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');

    expect($id)->toBeInstanceOf(EntityId::class);
});

// ─── Construction ─────────────────────────────────────────────────────────────

it('crée un PatientId valide depuis un UUID', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $id = PatientId::fromString($uuid);

    expect($id->value())->toBe($uuid);
});

it('génère un PatientId unique', function (): void {
    $a = PatientId::generate();
    $b = PatientId::generate();

    expect($a->equals($b))->toBeFalse();
});

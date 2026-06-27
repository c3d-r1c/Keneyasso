<?php

declare(strict_types=1);

use App\Core\Domain\DomainException;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientIntrouvable;

/**
 * PatientIntrouvable est levée quand un findById() ne trouve aucun résultat.
 * Le contrôleur l'attrape via DomainException pour renvoyer un HTTP 404
 * sans connaître les détails internes du module Patients.
 */

// ─── Héritage ─────────────────────────────────────────────────────────────────

it('PatientIntrouvable est une DomainException', function (): void {
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');

    expect(PatientIntrouvable::avecId($id))->toBeInstanceOf(DomainException::class);
});

// ─── Message ──────────────────────────────────────────────────────────────────

it('produit un message d\'erreur explicite avec l\'identifiant', function (): void {
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');

    $exception = PatientIntrouvable::avecId($id);

    expect($exception->getMessage())
        ->toContain('550e8400-e29b-41d4-a716-446655440000');
});

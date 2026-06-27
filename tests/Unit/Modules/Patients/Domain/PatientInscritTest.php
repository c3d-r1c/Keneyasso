<?php

declare(strict_types=1);

use App\Core\Domain\DomainEvent;
use Modules\Patients\Domain\PatientInscrit;

/**
 * PatientInscrit est émis quand un patient est créé dans le système.
 * Les autres modules (Labo, Pharmacie…) l'écoutent pour initialiser
 * leur propre contexte sans coupler directement au module Patients.
 */

// ─── Héritage ─────────────────────────────────────────────────────────────────

it('PatientInscrit est un DomainEvent', function (): void {
    $event = new PatientInscrit('550e8400-e29b-41d4-a716-446655440000', 'Moussa TRAORÉ');

    expect($event)->toBeInstanceOf(DomainEvent::class);
});

// ─── Données transportées ─────────────────────────────────────────────────────

it('transporte l\'identifiant du patient', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $event = new PatientInscrit($uuid, 'Moussa TRAORÉ');

    expect($event->patientId)->toBe($uuid);
});

it('transporte le nom complet pour les notifications', function (): void {
    // Les abonnés (ex. service email) n'ont pas à aller chercher le Patient en BDD.
    $event = new PatientInscrit('550e8400-e29b-41d4-a716-446655440000', 'Moussa TRAORÉ');

    expect($event->nomComplet)->toBe('Moussa TRAORÉ');
});

it('est horodaté automatiquement', function (): void {
    $event = new PatientInscrit('550e8400-e29b-41d4-a716-446655440000', 'Moussa TRAORÉ');

    expect($event->occurredAt())->toBeInstanceOf(DateTimeImmutable::class);
});

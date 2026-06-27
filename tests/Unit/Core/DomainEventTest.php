<?php

declare(strict_types=1);

use App\Core\Domain\DomainEvent;

/**
 * PatientCreated simule un événement domaine réel.
 * Dans le module Patients, ce sera : PatientInscrit, DossierMisAJour, etc.
 * L'événement embarque les données métier nécessaires aux autres modules.
 */
class PatientCreated extends DomainEvent
{
    public function __construct(public readonly string $patientId)
    {
        parent::__construct(); // horodatage automatique via DomainEvent
    }
}

// ─── Horodatage ───────────────────────────────────────────────────────────────

it('un DomainEvent a une date d\'occurrence', function (): void {
    // L'horodatage est automatique à la construction — pas besoin de le passer.
    $event = new PatientCreated('123');

    expect($event->occurredAt())->toBeInstanceOf(DateTimeImmutable::class);
});

// ─── Données métier ───────────────────────────────────────────────────────────

it('un DomainEvent expose ses données métier', function (): void {
    // Les abonnés à l'événement (module Pharmacie, Labo…) lisent les données via les propriétés publiques.
    $event = new PatientCreated('abc-123');

    expect($event->patientId)->toBe('abc-123');
});

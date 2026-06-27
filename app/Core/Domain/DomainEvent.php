<?php

declare(strict_types=1);

namespace App\Core\Domain;

use DateTimeImmutable;

/**
 * Fait domaine passé et immuable.
 *
 * Un DomainEvent décrit quelque chose qui s'est produit dans le domaine métier :
 * "PatientInscrit", "ConsultationTerminée", "OrdonnanceEmise"…
 *
 * Les événements sont le seul canal de communication inter-modules :
 * le module Pharmacie écoute "OrdonnanceEmise" sans jamais appeler
 * directement le module Consultation.
 *
 * L'événement est enregistré dans l'AggregateRoot via record() puis
 * dispatché par l'Infrastructure après la sauvegarde en base —
 * jamais depuis le Domain lui-même (pas de Event::dispatch() ici).
 *
 * Usage :
 *   class PatientInscrit extends DomainEvent {
 *       public function __construct(public readonly string $patientId) {
 *           parent::__construct();
 *       }
 *   }
 */
abstract class DomainEvent
{
    private readonly DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable;
    }

    /** Moment où l'événement s'est produit (horodatage automatique à la création). */
    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

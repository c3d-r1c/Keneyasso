<?php

declare(strict_types=1);

namespace App\Core\Domain;

use Carbon\CarbonImmutable;

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
 * dispatché par le Handler après la sauvegarde en base (via pullDomainEvents()).
 * Jamais depuis le Domain lui-même.
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
    private readonly CarbonImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = CarbonImmutable::now();
    }

    /** Moment où l'événement s'est produit (horodatage automatique à la création). */
    public function occurredAt(): CarbonImmutable
    {
        return $this->occurredAt;
    }
}

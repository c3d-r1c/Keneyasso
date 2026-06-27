<?php

declare(strict_types=1);

namespace App\Core\Domain;

/**
 * Racine d'agrégat : entité principale qui garantit la cohérence de son groupe.
 *
 * Un agrégat est un cluster d'objets domaine traité comme une unité.
 * L'AggregateRoot est le seul point d'entrée — on ne modifie jamais
 * directement ses objets internes depuis l'extérieur.
 *
 * Gestion des événements domaine :
 *   1. L'agrégat enregistre ses événements via record() pendant une action métier.
 *   2. L'Infrastructure appelle pullDomainEvents() après save() pour les dispatcher.
 *   3. La liste est vidée à chaque lecture — pas de double dispatch.
 *
 * Usage :
 *   class Patient extends AggregateRoot {
 *       public static function inscrire(PatientId $id, Nom $nom): self {
 *           $patient = new self($id, $nom);
 *           $patient->record(new PatientInscrit($id->value()));
 *           return $patient;
 *       }
 *   }
 */
abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    /** Enregistre un événement domaine sans le dispatcher immédiatement. */
    protected function record(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Retourne et vide la liste des événements en attente de dispatch.
     * Appelé par l'Infrastructure après la persistance de l'agrégat.
     *
     * @return DomainEvent[]
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}

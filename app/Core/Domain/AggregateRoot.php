<?php

declare(strict_types=1);

namespace App\Core\Domain;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    protected function record(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return DomainEvent[] */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}

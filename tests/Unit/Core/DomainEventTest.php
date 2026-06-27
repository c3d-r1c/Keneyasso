<?php

declare(strict_types=1);

use App\Core\Domain\DomainEvent;

class PatientCreated extends DomainEvent
{
    public function __construct(public readonly string $patientId)
    {
        parent::__construct();
    }
}

it('un DomainEvent a une date d\'occurrence', function (): void {
    $event = new PatientCreated('123');

    expect($event->occurredAt())->toBeInstanceOf(DateTimeImmutable::class);
});

it('un DomainEvent expose ses données métier', function (): void {
    $event = new PatientCreated('abc-123');

    expect($event->patientId)->toBe('abc-123');
});

it('deux événements ont des dates distinctes ou identiques selon le moment', function (): void {
    $event = new PatientCreated('x');

    expect($event->occurredAt())->toBeInstanceOf(DateTimeImmutable::class);
});

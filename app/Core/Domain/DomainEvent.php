<?php

declare(strict_types=1);

namespace App\Core\Domain;

use DateTimeImmutable;

abstract class DomainEvent
{
    private readonly DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

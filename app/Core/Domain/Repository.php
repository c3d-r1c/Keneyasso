<?php

declare(strict_types=1);

namespace App\Core\Domain;

interface Repository
{
    public function save(AggregateRoot $entity): void;

    public function findById(EntityId $id): ?AggregateRoot;

    public function nextId(): EntityId;
}

<?php

declare(strict_types=1);

namespace App\Core\Domain;

abstract class ValueObject
{
    abstract public function equals(self $other): bool;
}

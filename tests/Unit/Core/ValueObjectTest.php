<?php

declare(strict_types=1);

use App\Core\Domain\ValueObject;

class Money extends ValueObject
{
    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {}

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->amount === $other->amount
            && $this->currency === $other->currency;
    }
}

it('deux ValueObject avec les mêmes données sont égaux', function (): void {
    $a = new Money(1000, 'XOF');
    $b = new Money(1000, 'XOF');

    expect($a->equals($b))->toBeTrue();
});

it('deux ValueObject avec des données différentes ne sont pas égaux', function (): void {
    $a = new Money(1000, 'XOF');
    $b = new Money(2000, 'XOF');

    expect($a->equals($b))->toBeFalse();
});

it('un ValueObject de type différent n\'est pas égal', function (): void {
    $money = new Money(1000, 'XOF');
    $other = new Money(1000, 'EUR');

    expect($money->equals($other))->toBeFalse();
});

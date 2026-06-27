<?php

declare(strict_types=1);

use App\Core\Domain\ValueObject;

/**
 * Money illustre un ValueObject typique du domaine médical :
 * un montant de consultation, le prix d'un médicament, etc.
 * L'égalité se fait sur les attributs, pas sur l'identité de l'objet.
 */
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

// ─── Égalité par valeur ───────────────────────────────────────────────────────

it('deux ValueObject avec les mêmes données sont égaux', function (): void {
    // Deux billets de 1 000 XOF sont interchangeables — même valeur, même monnaie.
    $a = new Money(1000, 'XOF');
    $b = new Money(1000, 'XOF');

    expect($a->equals($b))->toBeTrue();
});

it('deux ValueObject avec des données différentes ne sont pas égaux', function (): void {
    // 1 000 XOF ≠ 2 000 XOF — le montant diffère.
    $a = new Money(1000, 'XOF');
    $b = new Money(2000, 'XOF');

    expect($a->equals($b))->toBeFalse();
});

it('un ValueObject de type différent n\'est pas égal', function (): void {
    // 1 000 XOF ≠ 1 000 EUR — même montant mais devise différente.
    $money = new Money(1000, 'XOF');
    $other = new Money(1000, 'EUR');

    expect($money->equals($other))->toBeFalse();
});

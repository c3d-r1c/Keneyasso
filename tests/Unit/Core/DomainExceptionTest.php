<?php

declare(strict_types=1);

use App\Core\Domain\DomainException;

class PatientIntrouvalble extends DomainException
{
    public static function avecId(string $id): self
    {
        return new self("Patient introuvable : {$id}");
    }
}

it('une DomainException est une RuntimeException', function (): void {
    $exception = PatientIntrouvalble::avecId('123');

    expect($exception)->toBeInstanceOf(RuntimeException::class);
});

it('une DomainException transporte un message métier', function (): void {
    $exception = PatientIntrouvalble::avecId('abc-123');

    expect($exception->getMessage())->toBe('Patient introuvable : abc-123');
});

it('on peut attraper une DomainException via son type parent', function (): void {
    expect(fn () => throw PatientIntrouvalble::avecId('x'))
        ->toThrow(DomainException::class);
});

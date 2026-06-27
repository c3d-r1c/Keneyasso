<?php

declare(strict_types=1);

use App\Core\Domain\DomainException;

/**
 * PatientIntrouvalble simule une exception métier d'un module.
 * Le pattern "named constructor" avec avecId() produit un message
 * lisible sans exposer le constructeur générique à l'appelant.
 */
class PatientIntrouvalble extends DomainException
{
    public static function avecId(string $id): self
    {
        return new self("Patient introuvable : {$id}");
    }
}

// ─── Hiérarchie ───────────────────────────────────────────────────────────────

it('une DomainException est une RuntimeException', function (): void {
    // Permet au framework (ExceptionHandler) d'attraper toutes les erreurs
    // métier en un seul catch(RuntimeException) si besoin.
    $exception = PatientIntrouvalble::avecId('123');

    expect($exception)->toBeInstanceOf(RuntimeException::class);
});

// ─── Message ──────────────────────────────────────────────────────────────────

it('une DomainException transporte un message métier', function (): void {
    // Le message est destiné aux logs et aux réponses API — il doit être explicite.
    $exception = PatientIntrouvalble::avecId('abc-123');

    expect($exception->getMessage())->toBe('Patient introuvable : abc-123');
});

// ─── Interception par type parent ─────────────────────────────────────────────

it('on peut attraper une DomainException via son type parent', function (): void {
    // Le contrôleur attrape DomainException (type parent) sans connaître
    // PatientIntrouvable spécifiquement — découplage Presentation / Domain.
    expect(fn () => throw PatientIntrouvalble::avecId('x'))
        ->toThrow(DomainException::class);
});

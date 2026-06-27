<?php

declare(strict_types=1);

use App\Core\Domain\AggregateRoot;
use App\Modules\Patients\Domain\DateDeNaissance;
use App\Modules\Patients\Domain\Nom;
use App\Modules\Patients\Domain\Patient;
use App\Modules\Patients\Domain\PatientId;
use App\Modules\Patients\Domain\PatientInscrit;

/**
 * Patient est l'AggregateRoot central du module.
 * Toute modification d'un patient passe par ses méthodes —
 * jamais par accès direct à ses propriétés depuis l'extérieur.
 */

// ─── Construction ─────────────────────────────────────────────────────────────

it('Patient est un AggregateRoot', function (): void {
    $patient = Patient::inscrire(
        PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Moussa', 'Traoré'),
        DateDeNaissance::fromString('1990-05-15'),
    );

    expect($patient)->toBeInstanceOf(AggregateRoot::class);
});

it('expose son identifiant', function (): void {
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');

    $patient = Patient::inscrire($id, new Nom('Moussa', 'Traoré'), DateDeNaissance::fromString('1990-05-15'));

    expect($patient->id())->toBe($id);
});

it('expose son nom', function (): void {
    $nom = new Nom('Moussa', 'Traoré');

    $patient = Patient::inscrire(
        PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        $nom,
        DateDeNaissance::fromString('1990-05-15'),
    );

    expect($patient->nom()->equals($nom))->toBeTrue();
});

it('expose sa date de naissance', function (): void {
    $dateDeNaissance = DateDeNaissance::fromString('1990-05-15');

    $patient = Patient::inscrire(
        PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Moussa', 'Traoré'),
        $dateDeNaissance,
    );

    expect($patient->dateDeNaissance()->equals($dateDeNaissance))->toBeTrue();
});

// ─── Événement domaine ────────────────────────────────────────────────────────

it('émet un événement PatientInscrit à la création', function (): void {
    // Vérifie que l'Infrastructure aura bien un événement à dispatcher après save().
    $patient = Patient::inscrire(
        PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Moussa', 'Traoré'),
        DateDeNaissance::fromString('1990-05-15'),
    );

    $events = $patient->pullDomainEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(PatientInscrit::class);
});

it('l\'événement PatientInscrit contient le bon identifiant', function (): void {
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');

    $patient = Patient::inscrire($id, new Nom('Moussa', 'Traoré'), DateDeNaissance::fromString('1990-05-15'));
    /** @var PatientInscrit $event */
    $event = $patient->pullDomainEvents()[0];

    expect($event->patientId)->toBe($id->value());
});

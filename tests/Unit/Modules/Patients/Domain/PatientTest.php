<?php

declare(strict_types=1);

use App\Core\Domain\AggregateRoot;
use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Nom;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientInscrit;

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

// ─── Reconstitution depuis la persistence ─────────────────────────────────────

it('reconstituer() recrée un Patient sans émettre d\'événement', function (): void {
    // Quand l'Infrastructure recharge un patient depuis la BDD, on ne doit pas
    // réémettre PatientInscrit — l'événement a déjà eu lieu lors de l'inscription.
    $patient = Patient::reconstituer(
        PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Moussa', 'Traoré'),
        DateDeNaissance::fromString('1990-05-15'),
    );

    expect($patient->pullDomainEvents())->toBeEmpty();
});

it('reconstituer() expose les mêmes données que inscrire()', function (): void {
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $nom = new Nom('Moussa', 'Traoré');
    $ddn = DateDeNaissance::fromString('1990-05-15');

    $patient = Patient::reconstituer($id, $nom, $ddn);

    expect($patient->id()->equals($id))->toBeTrue()
        ->and($patient->nom()->equals($nom))->toBeTrue()
        ->and($patient->dateDeNaissance()->equals($ddn))->toBeTrue();
});

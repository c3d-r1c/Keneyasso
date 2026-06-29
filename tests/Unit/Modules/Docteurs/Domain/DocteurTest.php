<?php

declare(strict_types=1);

use App\Core\Domain\AggregateRoot;
use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurInscrit;
use Modules\Docteurs\Domain\Nom;
use Modules\Docteurs\Domain\NumeroOrdre;
use Modules\Docteurs\Domain\Specialite;

/**
 * Docteur est l'AggregateRoot du module Docteurs.
 * Toute modification d'un médecin passe par ses méthodes — jamais par
 * accès direct depuis l'extérieur. Deux fabriques statiques :
 *   inscrire()     → nouveau médecin, émet DocteurInscrit
 *   reconstituer() → rechargement depuis la persistence, aucun événement
 */

// ─── Helpers ──────────────────────────────────────────────────────────────────

function unDocteur(): Docteur
{
    return Docteur::inscrire(
        DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Ibrahim', 'Coulibaly'),
        new Specialite('Cardiologie'),
        new NumeroOrdre('BF-12345'),
    );
}

// ─── Construction ─────────────────────────────────────────────────────────────

it('Docteur est un AggregateRoot', function (): void {
    expect(unDocteur())->toBeInstanceOf(AggregateRoot::class);
});

it('expose son identifiant', function (): void {
    $id = DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $docteur = Docteur::inscrire($id, new Nom('Ibrahim', 'Coulibaly'), new Specialite('Cardiologie'), new NumeroOrdre('BF-12345'));
    expect($docteur->id())->toBe($id);
});

it('expose son nom', function (): void {
    $nom = new Nom('Ibrahim', 'Coulibaly');
    $docteur = Docteur::inscrire(DocteurId::generate(), $nom, new Specialite('Cardiologie'), new NumeroOrdre('BF-12345'));
    expect($docteur->nom()->equals($nom))->toBeTrue();
});

it('expose sa spécialité', function (): void {
    $specialite = new Specialite('Cardiologie');
    $docteur = Docteur::inscrire(DocteurId::generate(), new Nom('Ibrahim', 'Coulibaly'), $specialite, new NumeroOrdre('BF-12345'));
    expect($docteur->specialite()->equals($specialite))->toBeTrue();
});

it('expose son numéro d\'ordre', function (): void {
    $numero = new NumeroOrdre('BF-12345');
    $docteur = Docteur::inscrire(DocteurId::generate(), new Nom('Ibrahim', 'Coulibaly'), new Specialite('Cardiologie'), $numero);
    expect($docteur->numeroOrdre()->equals($numero))->toBeTrue();
});

// ─── Événement domaine ────────────────────────────────────────────────────────

it('émet un événement DocteurInscrit à la création', function (): void {
    // L'Infrastructure dispatchera cet événement après save()
    // pour notifier les autres modules qu'un médecin a rejoint le système.
    $events = unDocteur()->pullDomainEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(DocteurInscrit::class);
});

it('l\'événement DocteurInscrit contient le bon identifiant', function (): void {
    $id = DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $docteur = Docteur::inscrire($id, new Nom('Ibrahim', 'Coulibaly'), new Specialite('Cardiologie'), new NumeroOrdre('BF-12345'));
    /** @var DocteurInscrit $event */
    $event = $docteur->pullDomainEvents()[0];
    expect($event->docteurId)->toBe($id->value());
});

it('l\'événement DocteurInscrit contient la spécialité', function (): void {
    $docteur = unDocteur();
    /** @var DocteurInscrit $event */
    $event = $docteur->pullDomainEvents()[0];
    expect($event->specialite)->toBe('Cardiologie');
});

// ─── Reconstitution depuis la persistence ─────────────────────────────────────

it('reconstituer() recrée un Docteur sans émettre d\'événement', function (): void {
    // Lors d'un rechargement depuis la BDD, DocteurInscrit ne doit pas
    // être rémis — l'événement a déjà été traité lors de l'inscription initiale.
    $docteur = Docteur::reconstituer(
        DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Ibrahim', 'Coulibaly'),
        new Specialite('Cardiologie'),
        new NumeroOrdre('BF-12345'),
    );
    expect($docteur->pullDomainEvents())->toBeEmpty();
});

it('reconstituer() expose les mêmes données que inscrire()', function (): void {
    $id = DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $nom = new Nom('Ibrahim', 'Coulibaly');
    $specialite = new Specialite('Cardiologie');
    $numero = new NumeroOrdre('BF-12345');

    $docteur = Docteur::reconstituer($id, $nom, $specialite, $numero);

    expect($docteur->id()->equals($id))->toBeTrue()
        ->and($docteur->nom()->equals($nom))->toBeTrue()
        ->and($docteur->specialite()->equals($specialite))->toBeTrue()
        ->and($docteur->numeroOrdre()->equals($numero))->toBeTrue();
});

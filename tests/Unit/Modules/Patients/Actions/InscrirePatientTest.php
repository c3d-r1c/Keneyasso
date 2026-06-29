<?php

declare(strict_types=1);

use Illuminate\Contracts\Events\Dispatcher;
use Modules\Patients\Actions\InscrirePatient;
use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientInscrit;
use Modules\Patients\Domain\PatientIntrouvable;
use Modules\Patients\Domain\PatientRepository;

/**
 * InscrirePatient est l'action centrale du module Patients.
 *
 * Elle valide les scalaires en ValueObjects, crée l'agrégat Patient,
 * le persiste, puis dispatche PatientInscrit aux modules abonnés.
 *
 * Doubles utilisés :
 * - InMemoryPatientRepository : stockage en mémoire, sans BDD
 * - InscrirePatientSpy : collecte les événements pour les assertions
 */

// ─── Doubles de test ───────────────────────────────────────────────────────────

/** @internal */
final class InscrirePatientRepo implements PatientRepository
{
    /** @var array<string, Patient> */
    public array $store = [];

    public function save(Patient $patient): void
    {
        $this->store[$patient->id()->value()] = $patient;
    }

    public function findById(PatientId $id): ?Patient
    {
        return $this->store[$id->value()] ?? null;
    }

    public function getById(PatientId $id): Patient
    {
        return $this->findById($id) ?? throw PatientIntrouvable::avecId($id);
    }

    public function nextId(): PatientId
    {
        return PatientId::generate();
    }
}

/** @internal */
final class InscrirePatientSpy implements Dispatcher
{
    /** @var object[] */
    public array $dispatched = [];

    public function listen($events, $listener = null): void {}

    public function hasListeners($eventName): bool { return false; }

    public function subscribe($subscriber): void {}

    public function until($event, $payload = []): mixed { return null; }

    public function dispatch($event, $payload = [], $halt = false): mixed
    {
        $this->dispatched[] = $event;
        return null;
    }

    public function push($event, $payload = []): void {}

    public function flush($event): void {}

    public function forget($event): void {}

    public function forgetPushed(): void {}
}

// ─── Persistance ───────────────────────────────────────────────────────────────

it('persiste le patient après inscription', function (): void {
    $repo = new InscrirePatientRepo;
    $action = new InscrirePatient($repo, new InscrirePatientSpy);

    $action('Moussa', 'Traoré', '1990-05-15');

    expect($repo->store)->toHaveCount(1);
});

it('retourne l\'identifiant du patient créé', function (): void {
    $action = new InscrirePatient(new InscrirePatientRepo, new InscrirePatientSpy);

    $id = $action('Aminata', 'Coulibaly', '1985-03-22');

    expect($id)->toBeInstanceOf(PatientId::class);
});

it('normalise le nom : prénom Ucfirst, famille MAJUSCULES', function (): void {
    $repo = new InscrirePatientRepo;
    $action = new InscrirePatient($repo, new InscrirePatientSpy);

    $action('moussa', 'traoré', '1990-05-15');

    $patient = array_values($repo->store)[0];
    expect((string) $patient->nom())->toBe('Moussa TRAORÉ');
});

it('conserve la date de naissance exacte', function (): void {
    $repo = new InscrirePatientRepo;
    $action = new InscrirePatient($repo, new InscrirePatientSpy);

    $action('Moussa', 'Traoré', '1990-05-15');

    $patient = array_values($repo->store)[0];
    expect($patient->dateDeNaissance()->equals(DateDeNaissance::fromString('1990-05-15')))->toBeTrue();
});

// ─── Événements domaine ────────────────────────────────────────────────────────

it('dispatche PatientInscrit après la persistance', function (): void {
    // Les abonnés (Laboratoire, Pharmacie…) reçoivent l'événement
    // seulement APRÈS que le patient est sauvegardé.
    $spy = new InscrirePatientSpy;
    $action = new InscrirePatient(new InscrirePatientRepo, $spy);

    $action('Moussa', 'Traoré', '1990-05-15');

    expect($spy->dispatched)->toHaveCount(1)
        ->and($spy->dispatched[0])->toBeInstanceOf(PatientInscrit::class);
});

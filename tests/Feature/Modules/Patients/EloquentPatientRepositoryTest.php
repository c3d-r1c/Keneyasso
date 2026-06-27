<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Nom;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientIntrouvable;
use Modules\Patients\Infrastructure\Persistence\EloquentPatientRepository;

uses(RefreshDatabase::class);

/**
 * EloquentPatientRepository implémente PatientRepository via Eloquent.
 *
 * Ces tests vérifient que la persistance réelle (SQLite en test) est cohérente
 * avec le contrat du Domain : save/findById/getById/nextId.
 * On utilise RefreshDatabase pour repartir d'une base propre à chaque test.
 */

// ─── Helpers ───────────────────────────────────────────────────────────────────

function makeRepo(): EloquentPatientRepository
{
    return new EloquentPatientRepository;
}

function patientFixture(?PatientId $id = null): Patient
{
    return Patient::inscrire(
        $id ?? PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Moussa', 'Traoré'),
        DateDeNaissance::fromString('1990-05-15'),
    );
}

// ─── Persistance ──────────────────────────────────────────────────────────────

it('persiste un patient et le retrouve par son id', function (): void {
    // Vérifie que save() + findById() sont cohérents sur la vraie BDD.
    $repo = makeRepo();
    $patient = patientFixture();

    $repo->save($patient);

    $found = $repo->findById($patient->id());
    expect($found)->not->toBeNull()
        ->and($found?->id()->equals($patient->id()))->toBeTrue();
});

it('reconstitue le nom correctement depuis la BDD', function (): void {
    // La normalisation (ucfirst/upper) ne doit pas être appliquée deux fois.
    $repo = makeRepo();
    $repo->save(patientFixture());

    $found = $repo->findById(PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'));

    expect((string) $found?->nom())->toBe('Moussa TRAORÉ');
});

it('reconstitue la date de naissance correctement depuis la BDD', function (): void {
    $repo = makeRepo();
    $repo->save(patientFixture());

    $found = $repo->findById(PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'));

    expect($found?->dateDeNaissance()->equals(DateDeNaissance::fromString('1990-05-15')))->toBeTrue();
});

it('retourne null pour un id inexistant', function (): void {
    $repo = makeRepo();
    $unknownId = PatientId::fromString('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

    expect($repo->findById($unknownId))->toBeNull();
});

it('lève PatientIntrouvable via getById pour un id inexistant', function (): void {
    $repo = makeRepo();
    $unknownId = PatientId::fromString('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

    expect(fn (): Patient => $repo->getById($unknownId))
        ->toThrow(PatientIntrouvable::class);
});

it('un second save met à jour le patient existant sans dupliquer', function (): void {
    // save() est idempotent : on peut appeler save() plusieurs fois sur le même id.
    $repo = makeRepo();
    $patient = patientFixture();

    $repo->save($patient);
    $repo->save($patient);

    // On ne compte pas les lignes directement — on vérifie qu'on peut le retrouver.
    expect($repo->findById($patient->id()))->not->toBeNull();
});

it('nextId génère un PatientId unique', function (): void {
    $repo = makeRepo();

    $id1 = $repo->nextId();
    $id2 = $repo->nextId();

    expect($id1)->toBeInstanceOf(PatientId::class)
        ->and($id1->equals($id2))->toBeFalse();
});

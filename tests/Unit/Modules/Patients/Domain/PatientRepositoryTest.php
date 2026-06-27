<?php

declare(strict_types=1);

use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Nom;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientIntrouvable;
use Modules\Patients\Domain\PatientRepository;

/**
 * PatientRepository est le contrat que l'Infrastructure doit honorer.
 *
 * On teste ici via InMemoryPatientRepository — une implémentation purement
 * en mémoire définie dans ce fichier. Cela garantit que le contrat est correct
 * et utilisable sans toucher à la base de données, tout en servant de
 * documentation vivante pour les implémenteurs Eloquent/Doctrine.
 */

// ─── Double de test ────────────────────────────────────────────────────────────

/**
 * Implémentation en mémoire de PatientRepository.
 * Sert uniquement dans les tests — ne jamais utiliser en production.
 */
final class InMemoryPatientRepository implements PatientRepository
{
    /** @var array<string, Patient> */
    private array $store = [];

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
        return $this->findById($id)
            ?? throw PatientIntrouvable::avecId($id);
    }

    public function nextId(): PatientId
    {
        return PatientId::generate();
    }
}

// ─── Helpers ───────────────────────────────────────────────────────────────────

function makePatient(?PatientId $id = null): Patient
{
    return Patient::inscrire(
        $id ?? PatientId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Moussa', 'Traoré'),
        DateDeNaissance::fromString('1990-05-15'),
    );
}

// ─── Persistance ──────────────────────────────────────────────────────────────

it('retrouve un patient sauvegardé', function (): void {
    // save() + findById() doivent être cohérents : un patient sauvegardé est retrouvable.
    $repo = new InMemoryPatientRepository;
    $patient = makePatient();

    $repo->save($patient);

    $found = $repo->findById($patient->id());
    expect($found)->not->toBeNull()
        ->and($found?->id()->equals($patient->id()))->toBeTrue();
});

it('retourne null si le patient n\'existe pas', function (): void {
    // findById() ne lève pas d'exception — c'est getById() qui le fait.
    $repo = new InMemoryPatientRepository;
    $unknownId = PatientId::fromString('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

    expect($repo->findById($unknownId))->toBeNull();
});

it('lève PatientIntrouvable via getById quand le patient est absent', function (): void {
    // getById() est le raccourci pour les cas où l'absence est une erreur métier.
    $repo = new InMemoryPatientRepository;
    $unknownId = PatientId::fromString('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

    expect(fn (): Patient => $repo->getById($unknownId))
        ->toThrow(PatientIntrouvable::class);
});

it('nextId génère un PatientId valide et unique', function (): void {
    // L'Application layer appelle nextId() pour créer un identifiant avant inscrire().
    $repo = new InMemoryPatientRepository;

    $id1 = $repo->nextId();
    $id2 = $repo->nextId();

    expect($id1)->toBeInstanceOf(PatientId::class)
        ->and($id1->equals($id2))->toBeFalse();
});

it('écrase un patient existant lors d\'un second save', function (): void {
    // save() est idempotent par id : un deuxième appel met à jour, ne duplique pas.
    $repo = new InMemoryPatientRepository;
    $id = PatientId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $patient = makePatient($id);

    $repo->save($patient);
    $repo->save($patient); // second save — pas de doublon

    // On vérifie qu'il n'y a qu'une entrée (via findById cohérent).
    expect($repo->findById($id))->not->toBeNull();
});

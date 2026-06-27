<?php

declare(strict_types=1);

use App\Modules\Patients\Application\InscrirePatientCommand;
use App\Modules\Patients\Application\InscrirePatientHandler;
use App\Modules\Patients\Domain\DateDeNaissance;
use App\Modules\Patients\Domain\Nom;
use App\Modules\Patients\Domain\Patient;
use App\Modules\Patients\Domain\PatientId;
use App\Modules\Patients\Domain\PatientInscrit;
use App\Modules\Patients\Domain\PatientIntrouvable;
use App\Modules\Patients\Domain\PatientRepository;

/**
 * InscrirePatientHandler orchestre l'inscription d'un nouveau patient.
 *
 * Il reçoit un Command (DTO scalaire), délègue la création au Domain,
 * et persiste via le Repository. L'événement PatientInscrit est enregistré
 * dans l'agrégat — l'Infrastructure le dispatche après save().
 *
 * Double utilisé : InMemoryPatientRepository (défini dans ce fichier).
 */

// ─── Double de test ────────────────────────────────────────────────────────────

/** @internal */
final class InscrireInMemoryPatientRepository implements PatientRepository
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
        return $this->findById($id)
            ?? throw PatientIntrouvable::avecId($id);
    }

    public function nextId(): PatientId
    {
        return PatientId::generate();
    }
}

// ─── Comportement du Handler ───────────────────────────────────────────────────

it('inscrit un patient et le persiste dans le repository', function (): void {
    // La règle principale : après handle(), le patient est sauvegardé.
    $repo = new InscrireInMemoryPatientRepository;
    $handler = new InscrirePatientHandler($repo);

    $command = new InscrirePatientCommand(
        prenom: 'Moussa',
        nomDeFamille: 'Traoré',
        dateDeNaissance: '1990-05-15',
    );

    $handler->handle($command);

    expect($repo->store)->toHaveCount(1);
});

it('retourne l\'identifiant du patient créé', function (): void {
    // Le Handler retourne le PatientId pour que le Controller puisse rediriger.
    $repo = new InscrireInMemoryPatientRepository;
    $handler = new InscrirePatientHandler($repo);

    $command = new InscrirePatientCommand('Aminata', 'Coulibaly', '1985-03-22');

    $patientId = $handler->handle($command);

    expect($patientId)->toBeInstanceOf(PatientId::class);
});

it('le patient sauvegardé a le bon nom', function (): void {
    // Vérifie que le Command est correctement traduit en ValueObject Nom.
    $repo = new InscrireInMemoryPatientRepository;
    $handler = new InscrirePatientHandler($repo);

    $handler->handle(new InscrirePatientCommand('Moussa', 'Traoré', '1990-05-15'));

    $patient = array_values($repo->store)[0];
    expect((string) $patient->nom())->toBe('Moussa TRAORÉ');
});

it('le patient sauvegardé a la bonne date de naissance', function (): void {
    // Vérifie que la date est correctement transmise au Domain.
    $repo = new InscrireInMemoryPatientRepository;
    $handler = new InscrirePatientHandler($repo);

    $handler->handle(new InscrirePatientCommand('Moussa', 'Traoré', '1990-05-15'));

    $patient = array_values($repo->store)[0];
    expect($patient->dateDeNaissance()->equals(DateDeNaissance::fromString('1990-05-15')))->toBeTrue();
});

it('un événement PatientInscrit est enregistré dans l\'agrégat après handle()', function (): void {
    // L'Infrastructure dispatche pullDomainEvents() juste après save() —
    // ce test garantit que l'événement est bien présent à ce moment.
    $repo = new InscrireInMemoryPatientRepository;
    $handler = new InscrirePatientHandler($repo);

    $handler->handle(new InscrirePatientCommand('Moussa', 'Traoré', '1990-05-15'));

    $patient = array_values($repo->store)[0];
    $events = $patient->pullDomainEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(PatientInscrit::class);
});

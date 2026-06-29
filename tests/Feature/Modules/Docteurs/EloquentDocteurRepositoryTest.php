<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurIntrouvable;
use Modules\Docteurs\Domain\Nom;
use Modules\Docteurs\Domain\NumeroOrdre;
use Modules\Docteurs\Domain\Specialite;
use Modules\Docteurs\Repositories\EloquentDocteurRepository;

uses(RefreshDatabase::class);

/**
 * Teste l'implémentation Eloquent du DocteurRepository.
 * Ces tests touchent la base de données SQLite en mémoire (RefreshDatabase)
 * pour valider que la traduction Domain ↔ SQL est correcte de bout en bout.
 *
 * Fixture : un médecin Ibrahim COULIBALY, Cardiologue, numéro BF-12345.
 */

// ─── Helpers ──────────────────────────────────────────────────────────────────

function unDocteurDomain(): Docteur
{
    return Docteur::inscrire(
        DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000'),
        new Nom('Ibrahim', 'Coulibaly'),
        new Specialite('Cardiologie'),
        new NumeroOrdre('BF-12345'),
    );
}

// ─── save() ───────────────────────────────────────────────────────────────────

it('persiste un docteur en base', function (): void {
    $repo = new EloquentDocteurRepository;
    $repo->save(unDocteurDomain());

    $this->assertDatabaseHas('docteurs', [
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'COULIBALY',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'BF-12345',
    ]);
});

it('save() est idempotent — un deuxième save ne crée pas de doublon', function (): void {
    $repo = new EloquentDocteurRepository;
    $docteur = unDocteurDomain();
    $repo->save($docteur);
    $repo->save($docteur);

    $this->assertDatabaseCount('docteurs', 1);
});

// ─── findById() ───────────────────────────────────────────────────────────────

it('retrouve un docteur par son identifiant', function (): void {
    $repo = new EloquentDocteurRepository;
    $repo->save(unDocteurDomain());

    $trouve = $repo->findById(DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000'));

    expect($trouve)->toBeInstanceOf(Docteur::class)
        ->and((string) $trouve->nom())->toBe('Ibrahim COULIBALY')
        ->and((string) $trouve->specialite())->toBe('Cardiologie')
        ->and((string) $trouve->numeroOrdre())->toBe('BF-12345');
});

it('retourne null si le docteur n\'existe pas', function (): void {
    $repo = new EloquentDocteurRepository;
    expect($repo->findById(DocteurId::generate()))->toBeNull();
});

// ─── getById() ────────────────────────────────────────────────────────────────

it('getById() retourne le docteur quand il existe', function (): void {
    $repo = new EloquentDocteurRepository;
    $repo->save(unDocteurDomain());

    $docteur = $repo->getById(DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000'));
    expect($docteur)->toBeInstanceOf(Docteur::class);
});

it('getById() lève DocteurIntrouvable si le docteur est absent', function (): void {
    $repo = new EloquentDocteurRepository;
    expect(fn () => $repo->getById(DocteurId::generate()))->toThrow(DocteurIntrouvable::class);
});

// ─── reconstitution ───────────────────────────────────────────────────────────

it('findById() reconstruit le docteur sans émettre d\'événement', function (): void {
    // findById() appelle reconstituer() — aucun DocteurInscrit ne doit être émis.
    $repo = new EloquentDocteurRepository;
    $repo->save(unDocteurDomain());

    $docteur = $repo->findById(DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000'));
    expect($docteur->pullDomainEvents())->toBeEmpty();
});

// ─── nextId() ─────────────────────────────────────────────────────────────────

it('nextId() génère des identifiants uniques', function (): void {
    $repo = new EloquentDocteurRepository;
    expect($repo->nextId()->value())->not->toBe($repo->nextId()->value());
});

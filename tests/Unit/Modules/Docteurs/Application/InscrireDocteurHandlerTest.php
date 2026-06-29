<?php

declare(strict_types=1);

use Modules\Docteurs\Application\InscrireDocteurCommand;
use Modules\Docteurs\Application\InscrireDocteurHandler;
use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurInscrit;
use Modules\Docteurs\Domain\DocteurIntrouvable;
use Modules\Docteurs\Domain\DocteurRepository;
use Modules\Docteurs\Domain\Nom;

/**
 * InscrireDocteurHandler orchestre l'inscription d'un nouveau médecin.
 * Il traduit le Command (scalaires HTTP) en ValueObjects Domain,
 * délègue la création à l'AggregateRoot Docteur, et persiste via le Repository.
 *
 * Double de test : InscrireInMemoryDocteurRepository (défini dans ce fichier).
 */

// ─── Double de test ────────────────────────────────────────────────────────────

/** @internal */
final class InscrireInMemoryDocteurRepository implements DocteurRepository
{
    /** @var array<string, Docteur> */
    public array $store = [];

    public function save(Docteur $docteur): void
    {
        $this->store[$docteur->id()->value()] = $docteur;
    }

    public function findById(DocteurId $id): ?Docteur
    {
        return $this->store[$id->value()] ?? null;
    }

    public function getById(DocteurId $id): Docteur
    {
        return $this->findById($id) ?? throw DocteurIntrouvable::avecId($id);
    }

    public function nextId(): DocteurId
    {
        return DocteurId::generate();
    }
}

// ─── Comportement du Handler ───────────────────────────────────────────────────

it('inscrit un docteur et le persiste dans le repository', function (): void {
    // La règle principale : après handle(), le médecin est sauvegardé.
    $repo = new InscrireInMemoryDocteurRepository;
    $handler = new InscrireDocteurHandler($repo);

    $handler->handle(new InscrireDocteurCommand(
        prenom: 'Ibrahim',
        nomDeFamille: 'Coulibaly',
        specialite: 'Cardiologie',
        numeroOrdre: 'BF-12345',
    ));

    expect($repo->store)->toHaveCount(1);
});

it('retourne l\'identifiant du docteur créé', function (): void {
    // Le Handler retourne le DocteurId pour que le Controller puisse rediriger.
    $repo = new InscrireInMemoryDocteurRepository;
    $handler = new InscrireDocteurHandler($repo);

    $docteurId = $handler->handle(new InscrireDocteurCommand('Ibrahim', 'Coulibaly', 'Cardiologie', 'BF-12345'));

    expect($docteurId)->toBeInstanceOf(DocteurId::class);
});

it('le docteur sauvegardé a le bon nom', function (): void {
    // Vérifie que le Command est correctement traduit en ValueObject Nom.
    $repo = new InscrireInMemoryDocteurRepository;
    $handler = new InscrireDocteurHandler($repo);

    $handler->handle(new InscrireDocteurCommand('Ibrahim', 'Coulibaly', 'Cardiologie', 'BF-12345'));

    $docteur = array_values($repo->store)[0];
    expect((string) $docteur->nom())->toBe('Ibrahim COULIBALY');
});

it('le docteur sauvegardé a la bonne spécialité', function (): void {
    $repo = new InscrireInMemoryDocteurRepository;
    $handler = new InscrireDocteurHandler($repo);

    $handler->handle(new InscrireDocteurCommand('Ibrahim', 'Coulibaly', 'cardiologie', 'BF-12345'));

    $docteur = array_values($repo->store)[0];
    expect((string) $docteur->specialite())->toBe('Cardiologie');
});

it('le docteur sauvegardé a le bon numéro d\'ordre', function (): void {
    $repo = new InscrireInMemoryDocteurRepository;
    $handler = new InscrireDocteurHandler($repo);

    $handler->handle(new InscrireDocteurCommand('Ibrahim', 'Coulibaly', 'Cardiologie', '  BF-12345  '));

    $docteur = array_values($repo->store)[0];
    expect((string) $docteur->numeroOrdre())->toBe('BF-12345');
});

it('un événement DocteurInscrit est enregistré dans l\'agrégat après handle()', function (): void {
    // L'Infrastructure dispatchera pullDomainEvents() juste après save() —
    // ce test garantit que l'événement est bien présent à ce moment.
    $repo = new InscrireInMemoryDocteurRepository;
    $handler = new InscrireDocteurHandler($repo);

    $handler->handle(new InscrireDocteurCommand('Ibrahim', 'Coulibaly', 'Cardiologie', 'BF-12345'));

    $docteur = array_values($repo->store)[0];
    $events = $docteur->pullDomainEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(DocteurInscrit::class);
});

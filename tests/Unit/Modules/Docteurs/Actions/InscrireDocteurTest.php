<?php

declare(strict_types=1);

use Illuminate\Contracts\Events\Dispatcher;
use Modules\Docteurs\Actions\InscrireDocteur;
use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurInscrit;
use Modules\Docteurs\Domain\DocteurIntrouvable;
use Modules\Docteurs\Domain\DocteurRepository;

/**
 * InscrireDocteur est l'action centrale du module Docteurs.
 *
 * Elle valide les scalaires en ValueObjects, crée l'agrégat Docteur,
 * le persiste, puis dispatche DocteurInscrit aux modules abonnés.
 *
 * Doubles utilisés :
 * - InMemoryDocteurRepository : stockage en mémoire, sans BDD
 * - DocteurDispatcherSpy : collecte les événements pour les assertions
 */

// ─── Doubles de test ───────────────────────────────────────────────────────────

/** @internal */
final class InMemoryDocteurRepository implements DocteurRepository
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

/** @internal */
final class DocteurDispatcherSpy implements Dispatcher
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

it('persiste le médecin après inscription', function (): void {
    $repo = new InMemoryDocteurRepository;
    $action = new InscrireDocteur($repo, new DocteurDispatcherSpy);

    $action('Ibrahim', 'Coulibaly', 'Cardiologie', 'BF-12345');

    expect($repo->store)->toHaveCount(1);
});

it('retourne l\'identifiant du médecin créé', function (): void {
    $action = new InscrireDocteur(new InMemoryDocteurRepository, new DocteurDispatcherSpy);

    $id = $action('Ibrahim', 'Coulibaly', 'Cardiologie', 'BF-12345');

    expect($id)->toBeInstanceOf(DocteurId::class);
});

it('normalise le nom : prénom Ucfirst, famille MAJUSCULES', function (): void {
    $repo = new InMemoryDocteurRepository;
    $action = new InscrireDocteur($repo, new DocteurDispatcherSpy);

    $action('ibrahim', 'coulibaly', 'Cardiologie', 'BF-12345');

    $docteur = array_values($repo->store)[0];
    expect((string) $docteur->nom())->toBe('Ibrahim COULIBALY');
});

it('normalise la spécialité en Ucfirst', function (): void {
    $repo = new InMemoryDocteurRepository;
    $action = new InscrireDocteur($repo, new DocteurDispatcherSpy);

    $action('Ibrahim', 'Coulibaly', 'cardiologie', 'BF-12345');

    $docteur = array_values($repo->store)[0];
    expect((string) $docteur->specialite())->toBe('Cardiologie');
});

it('supprime les espaces superflus du numéro d\'ordre', function (): void {
    $repo = new InMemoryDocteurRepository;
    $action = new InscrireDocteur($repo, new DocteurDispatcherSpy);

    $action('Ibrahim', 'Coulibaly', 'Cardiologie', '  BF-12345  ');

    $docteur = array_values($repo->store)[0];
    expect((string) $docteur->numeroOrdre())->toBe('BF-12345');
});

// ─── Événements domaine ────────────────────────────────────────────────────────

it('dispatche DocteurInscrit après la persistance', function (): void {
    // Les abonnés reçoivent l'événement seulement APRÈS que le médecin est sauvegardé.
    $spy = new DocteurDispatcherSpy;
    $action = new InscrireDocteur(new InMemoryDocteurRepository, $spy);

    $action('Ibrahim', 'Coulibaly', 'Cardiologie', 'BF-12345');

    expect($spy->dispatched)->toHaveCount(1)
        ->and($spy->dispatched[0])->toBeInstanceOf(DocteurInscrit::class);
});

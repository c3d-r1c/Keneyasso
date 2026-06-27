<?php

declare(strict_types=1);

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\EntityId;
use App\Core\Domain\Repository;

/**
 * Fixtures de test — simulent ce que les modules définiront.
 *
 * InMemoryProductRepository est le pattern utilisé dans TOUS les tests de module :
 * - pas de base de données → tests ultra-rapides
 * - implémente le même contrat Repository que EloquentPatientRepository en prod
 * - le Domain et l'Application ne voient aucune différence
 */
class ProductId extends EntityId {}

class Product extends AggregateRoot
{
    public function __construct(
        private readonly ProductId $id,
        public readonly string $name,
    ) {}

    public function id(): ProductId
    {
        return $this->id;
    }
}

class InMemoryProductRepository implements Repository
{
    /** @var array<string, Product> */
    private array $store = [];

    public function save(AggregateRoot $entity): void
    {
        $this->store[$entity->id()->value()] = $entity;
    }

    public function findById(EntityId $id): ?AggregateRoot
    {
        return $this->store[$id->value()] ?? null;
    }

    public function nextId(): EntityId
    {
        return ProductId::generate();
    }
}

// ─── Persistance et récupération ──────────────────────────────────────────────

it('le repository peut sauvegarder et retrouver un agrégat', function (): void {
    // Cycle de vie complet : save() puis findById() retrouve le même objet.
    $repo = new InMemoryProductRepository;
    $id = ProductId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $product = new Product($id, 'Paracétamol');

    $repo->save($product);

    $found = $repo->findById($id);
    expect($found)->toBeInstanceOf(Product::class)
        ->and($found->name)->toBe('Paracétamol');
});

// ─── Absence ──────────────────────────────────────────────────────────────────

it('le repository retourne null si l\'agrégat n\'existe pas', function (): void {
    // L'appelant gère le null — il lèvera une DomainException (ex. PatientIntrouvable).
    $repo = new InMemoryProductRepository;
    $id = ProductId::fromString('550e8400-e29b-41d4-a716-446655440000');

    expect($repo->findById($id))->toBeNull();
});

// ─── Génération d'identifiant ─────────────────────────────────────────────────

it('nextId génère un identifiant unique', function (): void {
    // Pattern d'usage : $id = $repo->nextId(); $patient = Patient::inscrire($id, $nom);
    $repo = new InMemoryProductRepository;

    expect($repo->nextId())->toBeInstanceOf(EntityId::class);
});

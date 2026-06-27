<?php

declare(strict_types=1);

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\DomainEvent;
use App\Core\Domain\EntityId;

/**
 * Fixtures de test — simulent ce que les vrais modules définiront.
 * OrderId → PatientId / DoctorId
 * OrderPlaced → PatientInscrit / ConsultationOuverte
 * Order → Patient / Consultation
 */
class OrderId extends EntityId {}

class OrderPlaced extends DomainEvent
{
    public function __construct(public readonly string $orderId)
    {
        parent::__construct();
    }
}

class Order extends AggregateRoot
{
    public function __construct(private readonly OrderId $id) {}

    public function getId(): OrderId
    {
        return $this->id;
    }

    /**
     * Factory qui crée l'agrégat ET enregistre l'événement en une seule opération.
     * C'est le pattern standard : action métier → record() → l'Infrastructure dispatche après save().
     */
    public static function place(OrderId $id): self
    {
        $order = new self($id);
        $order->record(new OrderPlaced($id->value()));

        return $order;
    }
}

// ─── Enregistrement des événements ───────────────────────────────────────────

it('un AggregateRoot enregistre les événements domaine', function (): void {
    // Vérifie que record() accumule bien l'événement et qu'il est du bon type.
    $id = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $order = Order::place($id);

    $events = $order->pullDomainEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(OrderPlaced::class);
});

// ─── Vidage après lecture ─────────────────────────────────────────────────────

it('pullDomainEvents vide la liste après lecture', function (): void {
    // Garantit qu'un même événement ne sera jamais dispatché deux fois
    // même si l'Infrastructure appelle pullDomainEvents() plusieurs fois.
    $id = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $order = Order::place($id);

    $order->pullDomainEvents(); // premier appel — vide la liste

    expect($order->pullDomainEvents())->toBeEmpty(); // deuxième appel — liste vide
});

// ─── Accumulation dans l'ordre ────────────────────────────────────────────────

it('plusieurs événements sont enregistrés dans l\'ordre', function (): void {
    // Un agrégat peut générer plusieurs événements en une seule session
    // (ex. Patient créé → Dossier ouvert → Email de bienvenue planifié).
    // Tous doivent être dispatché dans l'ordre d'enregistrement.
    $id = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');

    $order = new class($id) extends AggregateRoot
    {
        public function __construct(private readonly OrderId $id) {}

        public function doSomething(): void
        {
            $this->record(new OrderPlaced($this->id->value()));
        }
    };

    $order->doSomething();
    $order->doSomething();

    expect($order->pullDomainEvents())->toHaveCount(2);
});

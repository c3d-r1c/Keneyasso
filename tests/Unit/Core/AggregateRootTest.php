<?php

declare(strict_types=1);

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\DomainEvent;
use App\Core\Domain\EntityId;

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

    public static function place(OrderId $id): self
    {
        $order = new self($id);
        $order->record(new OrderPlaced($id->value()));

        return $order;
    }
}

it('un AggregateRoot enregistre les événements domaine', function (): void {
    $id = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $order = Order::place($id);

    $events = $order->pullDomainEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(OrderPlaced::class);
});

it('pullDomainEvents vide la liste après lecture', function (): void {
    $id = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
    $order = Order::place($id);

    $order->pullDomainEvents();

    expect($order->pullDomainEvents())->toBeEmpty();
});

it('plusieurs événements sont enregistrés dans l\'ordre', function (): void {
    $id = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');

    // On simule deux actions successives via une sous-classe exposant record()
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

<?php

declare(strict_types=1);

use App\Core\Domain\DomainEvent;
use Modules\Docteurs\Domain\DocteurInscrit;

/**
 * DocteurInscrit est émis quand un médecin est enregistré dans le système.
 * Il transporte les données minimales pour que les abonnés (autres modules,
 * notifications) n'aient pas besoin de recharger le Docteur depuis la BDD.
 */

// ─── Structure ────────────────────────────────────────────────────────────────

it('DocteurInscrit est un DomainEvent', function (): void {
    $event = new DocteurInscrit('uuid-123', 'Ibrahim COULIBALY', 'Cardiologie');
    expect($event)->toBeInstanceOf(DomainEvent::class);
});

it('porte l\'identifiant du docteur', function (): void {
    $event = new DocteurInscrit('uuid-123', 'Ibrahim COULIBALY', 'Cardiologie');
    expect($event->docteurId)->toBe('uuid-123');
});

it('porte le nom complet du docteur', function (): void {
    $event = new DocteurInscrit('uuid-123', 'Ibrahim COULIBALY', 'Cardiologie');
    expect($event->nomComplet)->toBe('Ibrahim COULIBALY');
});

it('porte la spécialité du docteur', function (): void {
    $event = new DocteurInscrit('uuid-123', 'Ibrahim COULIBALY', 'Cardiologie');
    expect($event->specialite)->toBe('Cardiologie');
});

// ─── Horodatage ───────────────────────────────────────────────────────────────

it('est horodaté à sa création', function (): void {
    $before = new DateTimeImmutable;
    $event = new DocteurInscrit('uuid-123', 'Ibrahim COULIBALY', 'Cardiologie');
    $after = new DateTimeImmutable;

    expect($event->occurredAt())->toBeGreaterThanOrEqual($before)
        ->and($event->occurredAt())->toBeLessThanOrEqual($after);
});

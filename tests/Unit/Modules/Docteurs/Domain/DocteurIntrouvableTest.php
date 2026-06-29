<?php

declare(strict_types=1);

use App\Core\Domain\DomainException;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurIntrouvable;

/**
 * DocteurIntrouvable est levée quand un médecin est recherché par id
 * mais n'existe pas en base. Sous-type de DomainException pour que
 * la couche Presentation puisse attraper toutes les erreurs métier
 * sans connaître les types spécifiques de chaque module.
 */
it('est une DomainException', function (): void {
    $id = DocteurId::fromString('550e8400-e29b-41d4-a716-446655440000');
    expect(DocteurIntrouvable::avecId($id))->toBeInstanceOf(DomainException::class);
});

it('construit un message contenant l\'identifiant', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $id = DocteurId::fromString($uuid);
    $exception = DocteurIntrouvable::avecId($id);
    expect($exception->getMessage())->toContain($uuid);
});

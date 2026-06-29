<?php

declare(strict_types=1);

use Modules\Docteurs\Domain\NumeroOrdre;

/**
 * NumeroOrdre est le numéro d'ordre du Conseil de l'Ordre des Médecins.
 * C'est l'identifiant officiel qui prouve qu'un médecin est autorisé à exercer.
 * Le Domain vérifie uniquement qu'il n'est pas vide — le format exact
 * dépend du pays et peut varier (alphanumérique, avec tirets, etc.).
 */

// ─── Validation ───────────────────────────────────────────────────────────────

it('accepte un numéro d\'ordre valide', function (): void {
    $numero = new NumeroOrdre('BF-12345');
    expect($numero->valeur())->toBe('BF-12345');
});

it('rejette un numéro vide', function (): void {
    expect(fn (): \Modules\Docteurs\Domain\NumeroOrdre => new NumeroOrdre(''))->toThrow(InvalidArgumentException::class);
});

it('rejette un numéro composé uniquement d\'espaces', function (): void {
    expect(fn (): \Modules\Docteurs\Domain\NumeroOrdre => new NumeroOrdre('   '))->toThrow(InvalidArgumentException::class);
});

// ─── Normalisation ────────────────────────────────────────────────────────────

it('supprime les espaces superflus', function (): void {
    expect((new NumeroOrdre('  BF-12345  '))->valeur())->toBe('BF-12345');
});

// ─── Égalité ──────────────────────────────────────────────────────────────────

it('deux NumeroOrdre identiques sont égaux', function (): void {
    expect((new NumeroOrdre('BF-12345'))->equals(new NumeroOrdre('BF-12345')))->toBeTrue();
});

it('deux NumeroOrdre différents ne sont pas égaux', function (): void {
    expect((new NumeroOrdre('BF-12345'))->equals(new NumeroOrdre('BF-99999')))->toBeFalse();
});

// ─── Représentation ───────────────────────────────────────────────────────────

it('se représente comme sa valeur', function (): void {
    expect((string) new NumeroOrdre('BF-12345'))->toBe('BF-12345');
});

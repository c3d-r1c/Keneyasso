<?php

declare(strict_types=1);

namespace App\Core\Domain;

/**
 * Objet valeur : immuable, sans identité propre, égal par ses attributs.
 *
 * Un ValueObject n'a pas d'ID — deux instances avec les mêmes données
 * représentent la même chose (ex. deux billets de 5 000 XOF sont identiques).
 * C'est la différence avec une Entity, qui reste la même même si ses données changent.
 *
 * Usage :
 *   class NumeroDeTelephone extends ValueObject {
 *       public function __construct(private readonly string $numero) {}
 *       public function equals(ValueObject $other): bool {
 *           return $other instanceof self && $this->numero === $other->numero;
 *       }
 *   }
 */
abstract class ValueObject
{
    abstract public function equals(self $other): bool;
}

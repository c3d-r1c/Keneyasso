<?php

declare(strict_types=1);

namespace Modules\Docteurs\Application;

/**
 * Commande d'inscription d'un médecin.
 *
 * DTO immuable de scalaires — aucune dépendance Laravel ni Domain.
 * Le Handler est responsable de traduire ces scalaires en ValueObjects.
 */
final readonly class InscrireDocteurCommand
{
    public function __construct(
        public string $prenom,
        public string $nomDeFamille,
        public string $specialite,
        public string $numeroOrdre,
    ) {}
}

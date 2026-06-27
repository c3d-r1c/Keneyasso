<?php

declare(strict_types=1);

namespace App\Modules\Patients\Application;

/**
 * DTO scalaire transportant l'intention d'inscrire un patient.
 *
 * Le Command ne contient que des scalaires PHP — pas de ValueObjects.
 * C'est le Handler qui construit les ValueObjects et valide les règles métier.
 * Cela permet de sérialiser/deserialiser le Command (queue, HTTP, CLI) sans
 * dépendre du Domain dans la couche transport.
 *
 * Usage :
 *   $command = new InscrirePatientCommand('Moussa', 'Traoré', '1990-05-15');
 *   $patientId = $handler->handle($command);
 */
final readonly class InscrirePatientCommand
{
    public function __construct(
        public string $prenom,
        public string $nomDeFamille,
        public string $dateDeNaissance,
    ) {}
}

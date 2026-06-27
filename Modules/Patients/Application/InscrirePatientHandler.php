<?php

declare(strict_types=1);

namespace Modules\Patients\Application;

use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Nom;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientRepository;

/**
 * Orchestre l'inscription d'un nouveau patient.
 *
 * Le Handler est le seul endroit où le Command (scalaires) est traduit
 * en ValueObjects Domain. Il délègue ensuite toute la logique métier
 * à l'AggregateRoot Patient, puis persiste via le Repository.
 *
 * L'Infrastructure est responsable de dispatcher les DomainEvents
 * après l'appel à save() (via pullDomainEvents()).
 *
 * Usage dans un Controller :
 *   $id = $handler->handle(new InscrirePatientCommand(...$request->validated()));
 *   return redirect()->route('patients.show', $id);
 */
final readonly class InscrirePatientHandler
{
    public function __construct(
        private PatientRepository $repository,
    ) {}

    /**
     * Exécute la commande et retourne l'identifiant du patient créé.
     */
    public function handle(InscrirePatientCommand $command): PatientId
    {
        $id = $this->repository->nextId();

        $patient = Patient::inscrire(
            $id,
            new Nom($command->prenom, $command->nomDeFamille),
            DateDeNaissance::fromString($command->dateDeNaissance),
        );

        $this->repository->save($patient);

        return $id;
    }
}

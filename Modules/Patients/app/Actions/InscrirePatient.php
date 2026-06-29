<?php

declare(strict_types=1);

namespace Modules\Patients\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Modules\Patients\Domain\DateDeNaissance;
use Modules\Patients\Domain\Nom;
use Modules\Patients\Domain\Patient;
use Modules\Patients\Domain\PatientId;
use Modules\Patients\Domain\PatientRepository;

/**
 * Inscrit un nouveau patient dans le système.
 *
 * Action invocable : le container Laravel l'injecte directement dans le Controller,
 * sans configuration supplémentaire. Les dépendances (Repository, Dispatcher)
 * sont résolues automatiquement.
 *
 * Les DomainEvents sont dispatchés APRÈS la persistance — les abonnés
 * (Laboratoire, Pharmacie…) voient toujours un Patient déjà en base.
 *
 * Usage dans un Controller :
 *   public function store(InscrirePatientRequest $request, InscrirePatient $action): RedirectResponse
 *   {
 *       $id = $action($request->prenom(), $request->nomDeFamille(), $request->dateDeNaissance());
 *       return redirect()->route('doclinic.patient_details', $id);
 *   }
 */
final class InscrirePatient
{
    public function __construct(
        private readonly PatientRepository $repository,
        private readonly Dispatcher $events,
    ) {}

    public function __invoke(string $prenom, string $nomDeFamille, string $dateDeNaissance): PatientId
    {
        $id = $this->repository->nextId();

        $patient = Patient::inscrire(
            $id,
            new Nom($prenom, $nomDeFamille),
            DateDeNaissance::fromString($dateDeNaissance),
        );

        $this->repository->save($patient);

        foreach ($patient->pullDomainEvents() as $event) {
            $this->events->dispatch($event);
        }

        return $id;
    }
}

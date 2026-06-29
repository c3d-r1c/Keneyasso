<?php

declare(strict_types=1);

namespace Modules\Docteurs\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurRepository;
use Modules\Docteurs\Domain\Nom;
use Modules\Docteurs\Domain\NumeroOrdre;
use Modules\Docteurs\Domain\Specialite;

/**
 * Inscrit un nouveau médecin dans le système.
 *
 * Action invocable : le container Laravel l'injecte directement dans le Controller,
 * sans configuration supplémentaire. Les dépendances (Repository, Dispatcher)
 * sont résolues automatiquement.
 *
 * Les DomainEvents sont dispatchés APRÈS la persistance — les abonnés
 * voient toujours un Docteur déjà en base.
 *
 * Usage dans un Controller :
 *   public function store(InscrireDocteurRequest $request, InscrireDocteur $action): RedirectResponse
 *   {
 *       $id = $action($request->prenom(), $request->nomDeFamille(), $request->specialite(), $request->numeroOrdre());
 *       return redirect()->route('doclinic.doctors', $id);
 *   }
 */
final class InscrireDocteur
{
    public function __construct(
        private readonly DocteurRepository $repository,
        private readonly Dispatcher $events,
    ) {}

    public function __invoke(
        string $prenom,
        string $nomDeFamille,
        string $specialite,
        string $numeroOrdre,
    ): DocteurId {
        $id = $this->repository->nextId();

        $docteur = Docteur::inscrire(
            $id,
            new Nom($prenom, $nomDeFamille),
            new Specialite($specialite),
            new NumeroOrdre($numeroOrdre),
        );

        $this->repository->save($docteur);

        foreach ($docteur->pullDomainEvents() as $event) {
            $this->events->dispatch($event);
        }

        return $id;
    }
}

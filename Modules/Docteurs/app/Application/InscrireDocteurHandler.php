<?php

declare(strict_types=1);

namespace Modules\Docteurs\Application;

use Modules\Docteurs\Domain\Docteur;
use Modules\Docteurs\Domain\DocteurId;
use Modules\Docteurs\Domain\DocteurRepository;
use Modules\Docteurs\Domain\Nom;
use Modules\Docteurs\Domain\NumeroOrdre;
use Modules\Docteurs\Domain\Specialite;

/**
 * Orchestre l'inscription d'un nouveau médecin.
 *
 * Le Handler est le seul endroit où le Command (scalaires) est traduit
 * en ValueObjects Domain. Il délègue ensuite toute la logique métier
 * à l'AggregateRoot Docteur, puis persiste via le Repository.
 *
 * L'Infrastructure est responsable de dispatcher les DomainEvents
 * après l'appel à save() (via pullDomainEvents()).
 *
 * Usage dans un Controller :
 *   $id = $handler->handle(new InscrireDocteurCommand(...$request->validated()));
 *   return redirect()->route('docteurs.show', $id);
 */
final readonly class InscrireDocteurHandler
{
    public function __construct(
        private DocteurRepository $repository,
    ) {}

    /**
     * Exécute la commande et retourne l'identifiant du médecin créé.
     */
    public function handle(InscrireDocteurCommand $command): DocteurId
    {
        $id = $this->repository->nextId();

        $docteur = Docteur::inscrire(
            $id,
            new Nom($command->prenom, $command->nomDeFamille),
            new Specialite($command->specialite),
            new NumeroOrdre($command->numeroOrdre),
        );

        $this->repository->save($docteur);

        return $id;
    }
}

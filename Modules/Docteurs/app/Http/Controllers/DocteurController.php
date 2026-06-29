<?php

declare(strict_types=1);

namespace Modules\Docteurs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Docteurs\Application\InscrireDocteurCommand;
use Modules\Docteurs\Application\InscrireDocteurHandler;
use Modules\Docteurs\Http\Requests\InscrireDocteurRequest;

/**
 * Expose les actions HTTP du module Docteurs.
 *
 * Le Controller est un adaptateur mince : il traduit la requête HTTP
 * en Command, délègue au Handler, puis traduit le résultat en réponse HTTP.
 * Aucune logique métier ici — tout est dans le Domain et l'Application.
 */
final class DocteurController extends Controller
{
    public function __construct(
        private readonly InscrireDocteurHandler $handler,
    ) {}

    /**
     * Inscrit un nouveau médecin et redirige vers sa fiche.
     */
    public function store(InscrireDocteurRequest $request): RedirectResponse
    {
        $id = $this->handler->handle(new InscrireDocteurCommand(
            prenom: $request->prenom(),
            nomDeFamille: $request->nomDeFamille(),
            specialite: $request->specialite(),
            numeroOrdre: $request->numeroOrdre(),
        ));

        return redirect()->route('doclinic.doctors', ['id' => $id->value()]);
    }
}

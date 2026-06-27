<?php

declare(strict_types=1);

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Patients\Application\InscrirePatientCommand;
use Modules\Patients\Application\InscrirePatientHandler;
use Modules\Patients\Http\Requests\InscrirePatientRequest;

/**
 * Expose les actions HTTP du module Patients.
 *
 * Le Controller est un adaptateur mince : il traduit la requête HTTP
 * en Command, délègue au Handler, puis traduit le résultat en réponse HTTP.
 * Aucune logique métier ici — tout est dans le Domain et l'Application.
 */
final class PatientController extends Controller
{
    public function __construct(
        private readonly InscrirePatientHandler $handler,
    ) {}

    /**
     * Inscrit un nouveau patient et redirige vers sa fiche.
     */
    public function store(InscrirePatientRequest $request): RedirectResponse
    {
        $id = $this->handler->handle(new InscrirePatientCommand(
            prenom: $request->validated('prenom'),
            nomDeFamille: $request->validated('nom_de_famille'),
            dateDeNaissance: $request->validated('date_de_naissance'),
        ));

        return redirect()->route('patients.store', ['id' => $id->value()]);
    }
}

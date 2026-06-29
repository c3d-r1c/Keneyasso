<?php

declare(strict_types=1);

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Patients\Actions\InscrirePatient;
use Modules\Patients\Http\Requests\InscrirePatientRequest;

/**
 * Expose les actions HTTP du module Patients.
 *
 * Adaptateur mince : traduit la requête HTTP en scalaires, délègue à l'Action,
 * puis traduit le résultat en réponse HTTP. Aucune logique métier ici.
 */
final class PatientController extends Controller
{
    public function store(InscrirePatientRequest $request, InscrirePatient $action): RedirectResponse
    {
        $id = $action(
            $request->prenom(),
            $request->nomDeFamille(),
            $request->dateDeNaissance(),
        );

        return redirect()
            ->route('doclinic.patient_details', ['id' => $id->value()])
            ->with('notify_success', __('patients::labels.notif_patient_inscrit'));
    }
}

<?php

declare(strict_types=1);

namespace Modules\Docteurs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Docteurs\Actions\InscrireDocteur;
use Modules\Docteurs\Http\Requests\InscrireDocteurRequest;

/**
 * Expose les actions HTTP du module Docteurs.
 *
 * Adaptateur mince : traduit la requête HTTP en scalaires, délègue à l'Action,
 * puis traduit le résultat en réponse HTTP. Aucune logique métier ici.
 */
final class DocteurController extends Controller
{
    public function store(InscrireDocteurRequest $request, InscrireDocteur $action): RedirectResponse
    {
        $id = $action(
            $request->prenom(),
            $request->nomDeFamille(),
            $request->specialite(),
            $request->numeroOrdre(),
        );

        return redirect()->route('doclinic.doctors', ['id' => $id->value()]);
    }
}

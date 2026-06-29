<?php

declare(strict_types=1);

namespace Modules\Docteurs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valide les données HTTP avant de créer un InscrireDocteurCommand.
 *
 * La validation ici est de surface (présence, type, longueur) — les règles
 * métier profondes (format du numéro d'ordre, spécialité reconnue) restent
 * dans les ValueObjects du Domain. On évite ainsi la duplication de logique.
 */
final class InscrireDocteurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'prenom' => ['required', 'string', 'max:100'],
            'nom_de_famille' => ['required', 'string', 'max:100'],
            'specialite' => ['required', 'string', 'max:100'],
            'numero_ordre' => ['required', 'string', 'max:50'],
        ];
    }

    public function prenom(): string
    {
        return $this->string('prenom')->value();
    }

    public function nomDeFamille(): string
    {
        return $this->string('nom_de_famille')->value();
    }

    public function specialite(): string
    {
        return $this->string('specialite')->value();
    }

    public function numeroOrdre(): string
    {
        return $this->string('numero_ordre')->value();
    }
}

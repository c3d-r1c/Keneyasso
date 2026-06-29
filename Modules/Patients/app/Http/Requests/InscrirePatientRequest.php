<?php

declare(strict_types=1);

namespace Modules\Patients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valide les données HTTP avant de créer un InscrirePatientCommand.
 *
 * La validation ici est de surface (présence, type, format) — les règles
 * métier profondes (âge, cohérence) restent dans les ValueObjects du Domain.
 * On évite ainsi la duplication de logique entre FormRequest et Domain.
 */
final class InscrirePatientRequest extends FormRequest
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
            'date_de_naissance' => ['required', 'date_format:Y-m-d'],
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

    public function dateDeNaissance(): string
    {
        return $this->string('date_de_naissance')->value();
    }
}

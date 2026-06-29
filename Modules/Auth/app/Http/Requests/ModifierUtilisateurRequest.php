<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valide la modification d'un utilisateur par un administrateur.
 *
 * La règle d'unicité email exclut l'utilisateur en cours de modification
 * pour permettre de soumettre son propre email sans erreur.
 */
final class ModifierUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'nom'     => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function nom(): string
    {
        return (string) $this->input('nom');
    }

    public function email(): string
    {
        return (string) $this->input('email');
    }

    public function roleId(): int
    {
        return (int) $this->input('role_id');
    }
}

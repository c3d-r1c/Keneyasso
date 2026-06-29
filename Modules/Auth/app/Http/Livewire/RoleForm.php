<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Formulaire de création d'un rôle avec assignation de permissions.
 *
 * Émet l'événement 'roleCreated' après une sauvegarde réussie
 * pour que RoleTable puisse se rafraîchir si les deux composants
 * cohabitent sur la même page.
 */
final class RoleForm extends Component
{
    public string $name = '';

    /** @var int[] */
    public array $selectedPermissions = [];

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);

        if ($this->selectedPermissions !== []) {
            $role->syncPermissions(
                Permission::whereIn('id', $this->selectedPermissions)->get()
            );
        }

        $this->reset('name', 'selectedPermissions');
        $this->dispatch('roleCreated');
    }

    public function render(): View
    {
        return view('auth::livewire.role-form', [
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }
}

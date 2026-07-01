<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Gestionnaire complet des rôles : liste, création, suppression.
 *
 * Le panel de création est un modal Bootstrap contrôlé via @entangle —
 * Livewire détient l'état ($showModal), Alpine synchronise Bootstrap.
 * Aucun JS manuel dans la page.
 */
final class RoleTable extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $confirmingDeleteId = null;

    public ?int $editingRoleId = null;

    public string $search = '';

    public string $name = '';

    /** @var int[] */
    public array $selectedPermissions = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function roles(): LengthAwarePaginator
    {
        return Role::query()
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy('name')
            ->paginate(15);
    }

    #[Computed]
    public function permissions(): Collection
    {
        return Permission::orderBy('name')->get();
    }

    #[Computed]
    public function totalRoles(): int
    {
        return Role::count();
    }

    #[Computed]
    public function totalPermissions(): int
    {
        return Permission::count();
    }

    public function openModal(): void
    {
        $this->reset('editingRoleId', 'name', 'selectedPermissions');
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $role = Role::findById($id);
        $this->editingRoleId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn ($v) => (int) $v)->toArray();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reset('showModal', 'editingRoleId', 'name', 'selectedPermissions');
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        $uniqueRule = Rule::unique('roles', 'name');

        if ($this->editingRoleId !== null) {
            $uniqueRule = $uniqueRule->ignore($this->editingRoleId);
        }

        return [
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $isEdit = $this->editingRoleId !== null;

        if ($isEdit) {
            $role = Role::findById($this->editingRoleId);
            $role->update(['name' => $this->name]);
        } else {
            $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);
        }

        $role->syncPermissions(
            Permission::whereIn('id', $this->selectedPermissions)->get()
        );

        $this->closeModal();
        unset($this->roles, $this->totalRoles);

        $this->dispatch('notify',
            type: 'success',
            message: $isEdit
                ? __('auth::labels.notif_role_modifie')
                : __('auth::labels.notif_role_cree'),
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        Role::findById($id)->delete();

        $this->confirmingDeleteId = null;
        unset($this->roles, $this->totalRoles);

        $this->dispatch('notify', type: 'success', message: __('auth::labels.notif_role_supprime'));
    }

    public function render(): View
    {
        return view('auth::livewire.role-table');
    }
}

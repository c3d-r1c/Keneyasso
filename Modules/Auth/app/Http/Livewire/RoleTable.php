<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * Liste paginée et cherchable des rôles de l'application.
 *
 * Lecture directe sur les modèles Spatie — pas de couche Domain nécessaire
 * pour ce composant de consultation pure.
 */
final class RoleTable extends Component
{
    use WithPagination;

    public string $search = '';

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

    public function delete(int $id): void
    {
        Role::findById($id)->delete();

        unset($this->roles);
    }

    public function render(): View
    {
        return view('auth::livewire.role-table');
    }
}

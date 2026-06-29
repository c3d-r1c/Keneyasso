<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Auth\Actions\SupprimerUtilisateur;

/**
 * Liste paginée des utilisateurs avec recherche et suppression inline.
 *
 * La suppression passe par SupprimerUtilisateur (Action) pour respecter
 * la couche Application. La recherche filtre sur le nom ET l'email.
 */
final class UserTable extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $confirmingDeleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->with('roles')
            ->orderBy('name')
            ->paginate(15);
    }

    #[Computed]
    public function totalUtilisateurs(): int
    {
        return User::count();
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
        app(SupprimerUtilisateur::class)($id);

        $this->confirmingDeleteId = null;
        unset($this->users, $this->totalUtilisateurs);

        $this->dispatch('notify', type: 'success', message: __('auth::labels.notif_user_supprime'));
    }

    public function render(): View
    {
        return view('auth::livewire.user-table');
    }
}

<?php

declare(strict_types=1);

namespace Modules\Docteurs\Http\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Docteurs\Models\DocteurModel;

/**
 * Composant Livewire de lecture (CQRS read side) pour la liste des médecins.
 *
 * Utilise DocteurModel directement car c'est une lecture interne au module :
 * pas de violation d'architecture — Domain et Application ne sont pas impliqués.
 * La recherche porte sur prénom, nom de famille et spécialité.
 *
 * Usage : <livewire:docteurs.docteur-table />
 */
final class DocteurTable extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function docteurs(): LengthAwarePaginator
    {
        return DocteurModel::query()
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('prenom', 'like', "%{$this->search}%")
                    ->orWhere('nom_de_famille', 'like', "%{$this->search}%")
                    ->orWhere('specialite', 'like', "%{$this->search}%"),
            )
            ->orderBy('nom_de_famille')
            ->paginate(15);
    }

    public function render(): View
    {
        return view('docteurs::livewire.docteur-table');
    }
}

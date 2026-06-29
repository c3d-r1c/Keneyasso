<?php

declare(strict_types=1);

namespace Modules\Patients\Http\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Patients\Models\PatientModel;

/**
 * Composant Livewire de lecture (CQRS read side) pour la liste des patients.
 *
 * Utilise PatientModel directement car c'est une lecture interne au module :
 * pas de violation d'architecture — Domain et Application ne sont pas impliqués.
 * La recherche et la pagination sont gérées côté serveur.
 *
 * Usage : <livewire:patients.patient-table />
 */
final class PatientTable extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function patients(): LengthAwarePaginator
    {
        return PatientModel::query()
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('prenom', 'like', "%{$this->search}%")
                    ->orWhere('nom_de_famille', 'like', "%{$this->search}%"),
            )
            ->orderBy('nom_de_famille')
            ->paginate(15);
    }

    public function render(): View
    {
        return view('patients::livewire.patient-table');
    }
}

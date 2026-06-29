<div>
    <div class="mb-3">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            class="form-control"
            placeholder="Rechercher un patient..."
        >
    </div>

    <div class="table-responsive rounded card-table">
        <table class="table border-no" id="patients-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Date de naissance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->patients as $patient)
                    <tr class="hover-primary">
                        <td><span class="text-muted fs-12">{{ substr($patient->id, 0, 8) }}</span></td>
                        <td>{{ $patient->prenom }}</td>
                        <td>{{ $patient->nom_de_famille }}</td>
                        <td>{{ \Carbon\Carbon::parse($patient->date_de_naissance)->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown">
                                    <i class="fa fa-ellipsis-h"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('doclinic.patient_details', $patient->id) }}">
                                        Voir le dossier
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-30">
                            Aucun patient enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $this->patients->links() }}
    </div>
</div>

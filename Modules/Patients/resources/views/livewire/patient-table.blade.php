<div>
    <div class="mb-3">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            class="form-control"
            placeholder="{{ __('patients::labels.rechercher') }}"
        >
    </div>

    <div class="table-responsive rounded card-table">
        <table class="table border-no" id="patients-table">
            <thead>
                <tr>
                    <th>{{ __('patients::labels.col_id') }}</th>
                    <th>{{ __('patients::labels.col_prenom') }}</th>
                    <th>{{ __('patients::labels.col_nom') }}</th>
                    <th>{{ __('patients::labels.col_date_naissance') }}</th>
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
                                        {{ __('patients::labels.voir_dossier') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-30">
                            {{ __('patients::labels.aucun_patient') }}
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

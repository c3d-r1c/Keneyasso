<div>
    <div class="mb-3">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            class="form-control"
            placeholder="{{ __('docteurs::labels.rechercher') }}"
        >
    </div>

    <div class="table-responsive rounded card-table">
        <table class="table border-no" id="docteurs-table">
            <thead>
                <tr>
                    <th>{{ __('docteurs::labels.col_id') }}</th>
                    <th>{{ __('docteurs::labels.col_prenom') }}</th>
                    <th>{{ __('docteurs::labels.col_nom') }}</th>
                    <th>{{ __('docteurs::labels.col_specialite') }}</th>
                    <th>{{ __('docteurs::labels.col_numero_ordre') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->docteurs as $docteur)
                    <tr class="hover-primary">
                        <td><span class="text-muted fs-12">{{ substr($docteur->id, 0, 8) }}</span></td>
                        <td>{{ $docteur->prenom }}</td>
                        <td>{{ $docteur->nom_de_famille }}</td>
                        <td>{{ $docteur->specialite }}</td>
                        <td>{{ $docteur->numero_ordre }}</td>
                        <td>
                            <div class="btn-group">
                                <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown">
                                    <i class="fa fa-ellipsis-h"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('doclinic.doctors', $docteur->id) }}">
                                        {{ __('docteurs::labels.voir_profil') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-30">
                            {{ __('docteurs::labels.aucun_medecin') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $this->docteurs->links() }}
    </div>
</div>

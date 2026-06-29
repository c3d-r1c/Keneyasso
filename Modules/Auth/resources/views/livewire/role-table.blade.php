<div>
    <div class="mb-3">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            class="form-control"
            placeholder="Rechercher un rôle..."
        >
    </div>

    <div class="table-responsive rounded card-table">
        <table class="table border-no">
            <thead>
                <tr>
                    <th>Nom du rôle</th>
                    <th>Permissions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->roles as $role)
                    <tr class="hover-primary">
                        <td><span class="fw-600">{{ $role->name }}</span></td>
                        <td>
                            @foreach ($role->permissions as $permission)
                                <span class="badge bg-primary-light text-primary me-1">{{ $permission->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <button
                                wire:click="delete({{ $role->id }})"
                                wire:confirm="Supprimer le rôle « {{ $role->name }} » ?"
                                class="btn btn-sm btn-danger-light"
                            >
                                <i class="ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-30">
                            Aucun rôle enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $this->roles->links() }}
    </div>
</div>

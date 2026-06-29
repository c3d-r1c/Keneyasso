<div>
    {{-- Barre de recherche --}}
    <div class="box">
        <div class="box-header with-border">
            <div class="input-group">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    class="form-control"
                    placeholder="Rechercher un rôle..."
                >
                <div class="input-group-append">
                    <button class="btn" type="button">
                        <i class="icon-Search"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="media-list media-list-divided media-list-hover">
            @forelse ($this->roles as $role)
                <div class="media align-items-center">
                    <span class="badge badge-dot badge-primary"></span>

                    <div class="avatar avatar-lg bg-primary-light rounded-circle d-flex align-items-center justify-content-center ms-10">
                        <i class="icon-Lock-overturning text-primary fs-18">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </div>

                    <div class="media-body ms-15">
                        <p class="mb-1">
                            <strong>{{ $role->name }}</strong>
                            <small class="sidetitle text-muted ms-5">{{ $role->guard_name }}</small>
                        </p>

                        <div>
                            @forelse ($role->permissions as $permission)
                                <span class="badge bg-primary-light text-primary me-1 mb-1">
                                    {{ $permission->name }}
                                </span>
                            @empty
                                <small class="text-muted">Aucune permission assignée</small>
                            @endforelse
                        </div>
                    </div>

                    <div class="media-right gap-items">
                        <button
                            wire:click="delete({{ $role->id }})"
                            wire:confirm="Supprimer le rôle « {{ $role->name }} » ?"
                            class="media-action btn btn-sm btn-danger-light"
                            data-bs-toggle="tooltip"
                            title="Supprimer"
                        >
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="media align-items-center justify-content-center py-30">
                    <p class="text-muted mb-0">Aucun rôle enregistré.</p>
                </div>
            @endforelse
        </div>

        @if ($this->roles->hasPages())
            <div class="box-footer">
                {{ $this->roles->links() }}
            </div>
        @endif
    </div>
</div>

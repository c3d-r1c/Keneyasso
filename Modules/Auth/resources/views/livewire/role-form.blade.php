<div>
    <form wire:submit="save">
        <div class="mb-3">
            <label class="form-label fw-600">Nom du rôle</label>
            <input
                wire:model="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="ex: médecin, infirmier, admin..."
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if($permissions->isNotEmpty())
            <div class="mb-3">
                <label class="form-label fw-600">Permissions</label>
                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-4 col-6 mb-2">
                            <div class="form-check">
                                <input
                                    wire:model="selectedPermissions"
                                    type="checkbox"
                                    value="{{ $permission->id }}"
                                    id="perm-{{ $permission->id }}"
                                    class="form-check-input"
                                >
                                <label for="perm-{{ $permission->id }}" class="form-check-label">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn-primary">
            <i class="ti-save me-1"></i> Créer le rôle
        </button>
    </form>
</div>

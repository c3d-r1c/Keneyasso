<div>
    <form wire:submit="save">
        <div class="form-group mb-15">
            <label class="col-md-12 form-label fw-600">Nom du rôle</label>
            <div class="col-md-12">
                <input
                    wire:model="name"
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="ex : médecin, infirmier, admin..."
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if ($permissions->isNotEmpty())
            <div class="form-group mb-15">
                <label class="col-md-12 form-label fw-600">Permissions</label>
                <div class="col-md-12">
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-6 col-6 mb-10">
                                <div class="form-check">
                                    <input
                                        wire:model="selectedPermissions"
                                        type="checkbox"
                                        value="{{ $permission->id }}"
                                        id="perm-{{ $permission->id }}"
                                        class="filled-in form-check-input"
                                    >
                                    <label for="perm-{{ $permission->id }}" class="form-check-label mb-0">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="modal-footer px-0 pb-0">
            <button type="submit" class="btn btn-success">
                <i class="ti-save me-5"></i> Créer le rôle
            </button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                Annuler
            </button>
        </div>
    </form>
</div>

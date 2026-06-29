{{--
    @entangle('showModal') synchronise la propriété Livewire avec Alpine.
    Alpine pilote Bootstrap modal — Bootstrap déclenche hidden.bs.modal quand
    l'utilisateur ferme (ESC, backdrop), Alpine le relaie à Livewire.
    Aucun JS manuel dans la page parente.
--}}
<div
    x-data="{ show: @entangle('showModal').live }"
    x-init="
        const modal = new bootstrap.Modal($refs.roleModal);
        $watch('show', v => v ? modal.show() : modal.hide());
        $refs.roleModal.addEventListener('hidden.bs.modal', () => { show = false; });
    "
>
    <div class="row">

        {{-- ─── Liste des rôles ──────────────────────────────────────────────── --}}
        <div class="col-lg-9 col-md-8">
            <div class="box">
                <div class="box-header with-border">
                    <div class="input-group">
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="search"
                            class="form-control"
                            placeholder="{{ __('auth::labels.rechercher_role') }}"
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
                                        <small class="text-muted">{{ __('auth::labels.aucune_permission') }}</small>
                                    @endforelse
                                </div>
                            </div>

                            <div class="media-right gap-items">
                                @if ($confirmingDeleteId === $role->id)
                                    <span class="fs-12 text-danger me-5 fw-600">{{ __('ui.supprimer') }}</span>
                                    <button wire:click="delete({{ $role->id }})" class="btn btn-sm btn-danger me-5">
                                        {{ __('ui.oui') }}
                                    </button>
                                    <button wire:click="cancelDelete" class="btn btn-sm btn-default">
                                        {{ __('ui.non') }}
                                    </button>
                                @else
                                    <button
                                        wire:click="openEdit({{ $role->id }})"
                                        class="media-action btn btn-sm btn-info-light me-5"
                                        title="{{ __('auth::labels.title_modifier_perms') }}"
                                    >
                                        <i class="ti-pencil"></i>
                                    </button>
                                    <button
                                        wire:click="confirmDelete({{ $role->id }})"
                                        class="media-action btn btn-sm btn-danger-light"
                                        title="{{ __('ui.supprimer') }}"
                                    >
                                        <i class="ti-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="media align-items-center justify-content-center py-30">
                            <p class="text-muted mb-0">{{ __('auth::labels.aucun_role') }}</p>
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

        {{-- ─── Panneau latéral : stats + bouton ────────────────────────────── --}}
        <div class="col-lg-3 col-md-4">
            <div class="box no-shadow">
                <div class="box-body">
                    <a class="btn btn-outline btn-primary mb-5 d-flex justify-content-between"
                       href="javascript:void(0)">
                        {{ __('auth::labels.stat_roles') }}
                        <span class="pull-right">{{ $this->totalRoles }}</span>
                    </a>
                    <a class="btn btn-outline btn-info mb-5 d-flex justify-content-between"
                       href="javascript:void(0)">
                        {{ __('auth::labels.stat_permissions') }}
                        <span class="pull-right">{{ $this->totalPermissions }}</span>
                    </a>
                    <button
                        wire:click="openModal"
                        class="btn btn-success mt-10 d-block w-100 text-center"
                    >
                        {{ __('auth::labels.nouveau_role') }}
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ─── Modal droit : formulaire de création / édition ──────────────────── --}}
    <div x-ref="roleModal" class="modal modal-right fade" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">
                        {{ $editingRoleId ? __('auth::labels.modal_modifier_role') : __('auth::labels.modal_nouveau_role') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.annuler') }}"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit="save">
                        <div class="form-group mb-15">
                            <label class="form-label fw-600">{{ __('auth::labels.nom_du_role') }}</label>
                            <input
                                wire:model="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="{{ __('auth::labels.placeholder_role') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($this->permissions->isNotEmpty())
                            <div class="form-group mb-20">
                                <label class="form-label fw-600">{{ __('auth::labels.label_permissions') }}</label>
                                <div class="row">
                                    @foreach ($this->permissions as $permission)
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
                        @endif

                        <div class="modal-footer-uniform d-flex justify-content-between px-0">
                            <button type="button" class="btn btn-danger" wire:click="closeModal">
                                {{ __('ui.annuler') }}
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti-save me-5"></i>
                                {{ $editingRoleId ? __('ui.enregistrer') : __('auth::labels.btn_creer_role') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

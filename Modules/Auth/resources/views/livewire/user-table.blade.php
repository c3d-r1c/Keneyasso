<div>
    <div class="row">

        {{-- ─── Liste des utilisateurs ──────────────────────────────────────── --}}
        <div class="col-lg-9 col-md-8">
            <div class="box">
                <div class="box-header with-border">
                    <div class="input-group">
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="search"
                            class="form-control"
                            placeholder="{{ __('auth::labels.rechercher_user') }}"
                        >
                        <div class="input-group-append">
                            <button class="btn" type="button">
                                <i class="icon-Search"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="media-list media-list-divided media-list-hover">
                    @forelse ($this->users as $user)
                        <div class="media align-items-center">
                            <div class="avatar avatar-lg bg-info-light rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-Single-02 text-info fs-18">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </div>

                            <div class="media-body ms-15">
                                <p class="mb-1">
                                    <strong>{{ $user->name }}</strong>
                                    <small class="sidetitle text-muted ms-5">{{ $user->email }}</small>
                                </p>
                                <div>
                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-primary-light text-primary me-1">{{ $role->name }}</span>
                                    @empty
                                        <small class="text-muted">{{ __('auth::labels.aucun_role_assigne') }}</small>
                                    @endforelse
                                </div>
                            </div>

                            <div class="media-right gap-items">
                                @if ($confirmingDeleteId === $user->id)
                                    <span class="fs-12 text-danger me-5 fw-600">{{ __('ui.supprimer') }}</span>
                                    <button wire:click="delete({{ $user->id }})" class="btn btn-sm btn-danger me-5">
                                        {{ __('ui.oui') }}
                                    </button>
                                    <button wire:click="cancelDelete" class="btn btn-sm btn-default">
                                        {{ __('ui.non') }}
                                    </button>
                                @else
                                    <a
                                        href="{{ route('auth.users.edit', $user) }}"
                                        class="media-action btn btn-sm btn-info-light me-5"
                                        title="{{ __('auth::labels.btn_modifier') }}"
                                    >
                                        <i class="ti-pencil"></i>
                                    </a>
                                    <button
                                        wire:click="confirmDelete({{ $user->id }})"
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
                            <p class="text-muted mb-0">{{ __('auth::labels.aucun_user') }}</p>
                        </div>
                    @endforelse
                </div>

                @if ($this->users->hasPages())
                    <div class="box-footer">
                        {{ $this->users->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── Panneau latéral : stats ──────────────────────────────────────── --}}
        <div class="col-lg-3 col-md-4">
            <div class="box no-shadow">
                <div class="box-body">
                    <a class="btn btn-outline btn-primary mb-5 d-flex justify-content-between"
                       href="javascript:void(0)">
                        {{ __('auth::labels.stat_utilisateurs') }}
                        <span class="pull-right">{{ $this->totalUtilisateurs }}</span>
                    </a>
                    <a href="{{ route('auth.users.create') }}" class="btn btn-success mt-10 d-block w-100 text-center">
                        {{ __('auth::labels.nouvel_utilisateur') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

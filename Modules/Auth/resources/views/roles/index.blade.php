@extends('layouts.app')

@section('title', 'Rôles & permissions')

@section('content')
<div class="container-full">

    {{-- En-tête de page --}}
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Rôles & permissions</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="mdi mdi-home-outline"></i></a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Administration</li>
                            <li class="breadcrumb-item active" aria-current="page">Rôles & permissions</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">

            {{-- Liste des rôles --}}
            <div class="col-lg-9 col-md-8">
                <livewire:auth.role-table />
            </div>

            {{-- Panneau latéral : stats + bouton d'ajout --}}
            <div class="col-lg-3 col-md-4">
                <div class="box no-shadow">
                    <div class="box-body">
                        <a class="btn btn-outline btn-primary mb-5 d-flex justify-content-between"
                           href="javascript:void(0)">
                            Rôles
                            <span class="pull-right">{{ $totalRoles }}</span>
                        </a>
                        <a class="btn btn-outline btn-info mb-5 d-flex justify-content-between"
                           href="javascript:void(0)">
                            Permissions
                            <span class="pull-right">{{ $totalPermissions }}</span>
                        </a>
                        <a href="javascript:void(0)"
                           data-bs-toggle="modal"
                           data-bs-target="#roleModal"
                           class="btn btn-success mt-10 d-block text-center">
                            + Nouveau rôle
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- Modal : formulaire de création de rôle --}}
<div id="roleModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="roleModalLabel">Nouveau rôle</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <livewire:auth.role-form />
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('roleCreated', () => {
            const modalEl = document.getElementById('roleModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    });
</script>
@endpush
@endsection

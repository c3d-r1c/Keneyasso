@extends('layouts.app')

@section('title', 'Rôles & permissions')

@section('content')
<div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Rôles & permissions</h4>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">

            {{-- Formulaire de création --}}
            <div class="col-xl-4 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Nouveau rôle</h4>
                    </div>
                    <div class="box-body">
                        <livewire:auth.role-form />
                    </div>
                </div>
            </div>

            {{-- Liste des rôles --}}
            <div class="col-xl-8 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Rôles existants</h4>
                    </div>
                    <div class="box-body">
                        <livewire:auth.role-table />
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

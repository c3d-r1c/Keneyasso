@extends('layouts.app')

@section('title', __('auth::labels.page_utilisateurs'))

@section('content')
<div class="container-full">

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">{{ __('auth::labels.page_utilisateurs') }}</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="mdi mdi-home-outline"></i></a>
                            </li>
                            <li class="breadcrumb-item">{{ __('ui.administration') }}</li>
                            <li class="breadcrumb-item active">{{ __('auth::labels.page_utilisateurs') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <a href="{{ route('auth.users.create') }}" class="btn btn-success btn-sm">
                <i class="ti-plus me-5"></i> {{ __('auth::labels.nouvel_utilisateur') }}
            </a>
        </div>
    </div>

    <section class="content">
        <livewire:auth.user-table />
    </section>

</div>
@endsection

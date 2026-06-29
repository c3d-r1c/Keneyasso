@extends('layouts.app')

@section('title', __('auth::labels.page_roles'))

@section('content')
<div class="container-full">

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">{{ __('auth::labels.page_roles') }}</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="mdi mdi-home-outline"></i></a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">{{ __('ui.administration') }}</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('auth::labels.page_roles') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <livewire:auth.role-table />
    </section>

</div>
@endsection

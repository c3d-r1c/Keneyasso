@extends('layouts.app')

@section('title', __('auth::labels.page_creer_user'))

@section('content')
<div class="container-full">

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">{{ __('auth::labels.page_creer_user') }}</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="mdi mdi-home-outline"></i></a>
                            </li>
                            <li class="breadcrumb-item">{{ __('ui.administration') }}</li>
                            <li class="breadcrumb-item active">{{ __('auth::labels.breadcrumb_nouvel_user') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">{{ __('auth::labels.info_nouvel_user') }}</h4>
                    </div>
                    <div class="box-body p-40">

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('auth.users.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                                    <input
                                        type="text"
                                        name="nom"
                                        value="{{ old('nom') }}"
                                        class="form-control ps-15 bg-transparent @error('nom') is-invalid @enderror"
                                        placeholder="{{ __('auth::labels.placeholder_nom') }}"
                                        required
                                    >
                                </div>
                                @error('nom')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent"><i class="ti-email"></i></span>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="form-control ps-15 bg-transparent @error('email') is-invalid @enderror"
                                        placeholder="{{ __('auth::labels.placeholder_email') }}"
                                        required
                                    >
                                </div>
                                @error('email')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent"><i class="ti-lock"></i></span>
                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control ps-15 bg-transparent @error('password') is-invalid @enderror"
                                        placeholder="{{ __('auth::labels.placeholder_password') }}"
                                        required
                                    >
                                </div>
                                @error('password')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-20">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="icon-Lock-overturning"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <select
                                        name="role_id"
                                        class="form-control ps-15 @error('role_id') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">{{ __('auth::labels.choisir_role') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role_id')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-10">
                                    <i class="ti-save me-5"></i> {{ __('auth::labels.btn_creer_user') }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

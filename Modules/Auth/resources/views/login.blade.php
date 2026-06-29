@extends('layouts.auth')

@section('title', 'Connexion — Keneyasso')

@section('content')
<div class="container h-p100">
    <div class="row align-items-center justify-content-md-center h-p100">
        <div class="col-12">
            <div class="row justify-content-center g-0">
                <div class="col-lg-5 col-md-5 col-12">

                    <div class="bg-white rounded10 shadow-lg">
                        <div class="content-top-agile p-20 pb-0">
                            <h2 class="text-primary">Keneyasso</h2>
                            <p class="mb-0">{{ __('auth::labels.connexion_titre') }}</p>
                        </div>

                        <div class="p-40">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('login.attempt') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-transparent">
                                            <i class="ti-email"></i>
                                        </span>
                                        <input
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            class="form-control ps-15 bg-transparent @error('email') is-invalid @enderror"
                                            placeholder="{{ __('auth::labels.placeholder_email') }}"
                                            autofocus
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-transparent">
                                            <i class="ti-lock"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control ps-15 bg-transparent"
                                            placeholder="{{ __('auth::labels.placeholder_password') }}"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="checkbox">
                                            <input type="checkbox" id="remember" name="remember">
                                            <label for="remember">{{ __('auth::labels.se_souvenir') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mt-10">
                                        <button type="submit" class="btn btn-danger mt-10">
                                            {{ __('auth::labels.btn_connexion') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

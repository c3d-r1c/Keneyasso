@extends('layouts.app')

@section('title', $first?->label ?? 'Keneyasso')

@section('content')
<div class="container-full">
    <section class="content">

        @if($first && $first->homeComponent)

            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h4 class="page-title">{{ $first->label }}</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body">
                            @livewire($first->homeComponent)
                        </div>
                    </div>
                </div>
            </div>

        @else

            <div class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
                <div class="text-center">
                    <i class="icon-Settings-1 fs-60 text-muted mb-20 d-block"></i>
                    <h3 class="text-muted">Aucun module actif</h3>
                    <p class="text-muted">Activez un module via <code>php artisan module:enable {Nom}</code></p>
                </div>
            </div>

        @endif

    </section>
</div>
@endsection

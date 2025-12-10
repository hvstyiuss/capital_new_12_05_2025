@extends('layouts.app')

@section('title', 'Déplacements - Mes Entités')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Déplacements</h1>
            <p class="text-muted mb-0">Gérer les déplacements de vos agents</p>
        </div>
    </div>

    <!-- Entities and Periods -->
    @foreach($entites as $entite)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-building me-2"></i>{{ $entite->name }}
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Sélectionnez une période pour préparer les déplacements:</p>
                <div class="row g-3">
                    @foreach($periodes as $periode)
                        <div class="col-md-3">
                            <a href="{{ route('deplacements.by-entity', [
                                'type' => $entite->entiteInfo->type ?? 'central',
                                'periode' => $periode->id,
                                'entite' => $entite->id
                            ]) }}" class="text-decoration-none">
                                <div class="card border hover-shadow transition-all h-100">
                                    <div class="card-body text-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                                            <i class="fas fa-calendar-alt text-primary fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">{{ $periode->name }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endpush
@endsection




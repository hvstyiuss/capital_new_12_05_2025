@extends('layouts.app')

@section('title', 'Déplacements - ' . ucfirst($type))

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Déplacements - {{ ucfirst($type) }}</h1>
            <p class="text-muted mb-0">Sélectionnez une période</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Periods Grid -->
    <div class="row g-3">
        @foreach($periodes as $periode)
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" 
                     onclick="window.location.href='{{ route('deplacements.by-period', ['type' => $type, 'periode' => $periode->id]) }}'">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3">
                            <i class="fas fa-calendar-alt text-primary fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $periode->name }}</h5>
                        <p class="text-muted small mb-0">Période {{ $periode->name }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
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

.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush
@endsection




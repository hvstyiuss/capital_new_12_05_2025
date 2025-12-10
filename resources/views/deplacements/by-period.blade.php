@extends('layouts.app')

@section('title', 'Déplacements - ' . ucfirst($type) . ' - ' . $periode->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Déplacements - {{ ucfirst($type) }} - {{ $periode->name }}</h1>
            <p class="text-muted mb-0">Sélectionnez une entité</p>
        </div>
        <a href="{{ route('deplacements.by-type', ['type' => $type]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Entities List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-building me-2"></i>Entités ({{ $entites->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($entites->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($entites as $entite)
                        <a href="{{ route('deplacements.by-entity', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" 
                           class="list-group-item list-group-item-action border-0 px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-semibold">{{ $entite->name }}</h6>
                                    <small class="text-muted">{{ $entite->code ?? 'N/A' }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune entité trouvée pour cette période</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection




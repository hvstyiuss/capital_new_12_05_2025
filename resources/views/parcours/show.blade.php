@extends('layouts.app')

@section('title', 'Parcours Professionnel - ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Parcours Professionnel</h1>
            <p class="text-muted mb-0">{{ $user->fname }} {{ $user->lname }} (PPR: {{ $user->ppr }})</p>
        </div>
        <div>
            <a href="{{ route('parcours.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        @if($user->userInfo && $user->userInfo->photo)
                            <img src="{{ asset('storage/' . $user->userInfo->photo) }}" 
                                 alt="{{ $user->fname }} {{ $user->lname }}" 
                                 class="rounded-circle me-3" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" 
                                 style="width: 80px; height: 80px;">
                                <span class="text-primary fw-bold" style="font-size: 2rem;">
                                    {{ strtoupper(substr($user->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->lname ?? '', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $user->fname }} {{ $user->lname }}</h4>
                            <p class="text-muted mb-1">
                                <span class="badge bg-info">{{ $user->ppr }}</span>
                                @if($user->email)
                                    <span class="ms-2">{{ $user->email }}</span>
                                @endif
                            </p>
                            @php
                                $currentParcours = $parcours->where('date_fin', null)->first() 
                                    ?? $parcours->where('date_fin', '>=', now())->first();
                            @endphp
                            @if($currentParcours)
                                <p class="mb-0">
                                    <span class="badge bg-success">Actuellement: {{ $currentParcours->entite->name ?? 'N/A' }}</span>
                                    @php
                                        $isCurrentChef = $currentParcours->entite && $currentParcours->entite->chef_ppr === $currentParcours->ppr;
                                    @endphp
                                    @if($isCurrentChef)
                                        <span class="badge bg-warning text-dark ms-2">
                                            <i class="fas fa-crown me-1"></i>Chef
                                        </span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-muted small mb-1">Total Parcours</div>
                    <h3 class="mb-0 fw-bold">{{ $parcours->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Parcours Timeline -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-route text-primary me-2"></i>Historique du Parcours
            </h5>
        </div>
        <div class="card-body">
            @if($parcours->count() > 0)
                <div class="timeline">
                    @foreach($parcours as $index => $parcour)
                        @php
                            $isActive = $parcour->date_fin === null || $parcour->date_fin >= now();
                            $isFirst = $index === 0;
                            $isLast = $index === $parcours->count() - 1;
                        @endphp
                        <div class="timeline-item {{ $isActive ? 'active' : '' }}">
                            <div class="timeline-marker {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                <i class="fas fa-{{ $isActive ? 'check-circle' : 'circle' }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1 fw-bold">
                                            {{ $parcour->poste ?? 'Poste non spécifié' }}
                                        </h6>
                                        <p class="mb-1 text-primary fw-semibold">
                                            <i class="fas fa-building me-1"></i>{{ $parcour->entite->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        @if($isActive)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Actif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Terminé</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Date de début</small>
                                        <span class="fw-semibold">{{ $parcour->date_debut ? $parcour->date_debut->format('d/m/Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Date de fin</small>
                                        @if($parcour->date_fin)
                                            <span class="fw-semibold">{{ $parcour->date_fin->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-success">En cours</span>
                                        @endif
                                    </div>
                                </div>

                                @if($parcour->grade)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Grade</small>
                                        <span class="badge bg-secondary">{{ $parcour->grade->name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                @if($parcour->reason)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Raison</small>
                                        <span class="text-dark">{{ $parcour->reason }}</span>
                                    </div>
                                @endif

                                <div class="d-flex gap-2 flex-wrap">
                                    @php
                                        $isChefParcours = $parcour->entite && $parcour->entite->chef_ppr === $parcour->ppr;
                                    @endphp
                                    @if($isChefParcours)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-crown me-1"></i>Chef
                                        </span>
                                    @endif
                                </div>

                                @if(!$isLast)
                                    <div class="timeline-connector"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-route fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucun parcours enregistré</h5>
                    <p class="text-muted small">Cet utilisateur n'a pas encore de parcours professionnel</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
}

.timeline-item.active .timeline-content {
    border-left: 4px solid #28a745;
    background: #f8fff9;
}

.timeline-marker {
    position: absolute;
    left: -48px;
    top: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    z-index: 2;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #dee2e6;
    transition: all 0.3s ease;
}

.timeline-content:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateX(5px);
}

.timeline-connector {
    position: absolute;
    left: -30px;
    top: 36px;
    bottom: -20px;
    width: 2px;
    background: #dee2e6;
    z-index: 1;
}

.timeline-item:last-child .timeline-connector {
    display: none;
}
</style>
@endpush
@endsection


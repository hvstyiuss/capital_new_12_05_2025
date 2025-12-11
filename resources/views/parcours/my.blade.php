@extends('layouts.app')

@section('title', 'Mon Parcours')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header with Gradient -->
    <div class="mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-route text-white fs-3"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-1 fw-bold text-white">Mon Parcours</h1>
                            <p class="text-white text-opacity-75 mb-0">Votre parcours professionnel dans l'agence</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Card - Modern Design -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-gradient-primary text-white border-0 py-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        @if($user->userInfo && $user->userInfo->photo)
                            <img src="{{ asset('storage/' . $user->userInfo->photo) }}" 
                                 alt="{{ $user->fname }} {{ $user->lname }}" 
                                 class="rounded-circle border border-3 border-white shadow-lg" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-white bg-opacity-20 border border-3 border-white shadow-lg d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <span class="text-white fw-bold" style="font-size: 2.5rem;">
                                    {{ strtoupper(substr($user->fname ?? 'N', 0, 1)) }}{{ strtoupper(substr($user->lname ?? 'C', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        @if($currentParcours)
                            @php
                                $isCurrentChef = $currentParcours->entite && $currentParcours->entite->chef_ppr === $currentParcours->ppr;
                            @endphp
                            @if($isCurrentChef)
                                <span class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center border border-2 border-white shadow" 
                                      style="width: 32px; height: 32px;">
                                    <i class="fas fa-crown"></i>
                                </span>
                            @endif
                        @endif
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold text-white">{{ $user->fname }} {{ $user->lname }}</h3>
                        <p class="text-white text-opacity-75 mb-1">
                            <i class="fas fa-id-card me-2"></i><strong>PPR:</strong> {{ $user->ppr }}
                        </p>
                        @if($user->email)
                            <p class="text-white text-opacity-75 mb-0">
                                <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="mt-3 mt-md-0">
                    <div class="bg-white bg-opacity-20 rounded-3 p-3 text-center" style="backdrop-filter: blur(10px);">
                        <div class="text-white text-opacity-75 small mb-1">Total Parcours</div>
                        <h2 class="mb-0 fw-bold text-white">{{ $parcours->unique('id')->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if($currentParcours)
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="alert alert-primary border-0 mb-0" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(10, 88, 202, 0.1) 100%);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-building text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-primary">Affectation Actuelle</h6>
                                    <p class="mb-0 fw-semibold">{{ $currentParcours->entite->name ?? 'N/A' }}</p>
                                    @if($currentParcours->poste)
                                        <small class="text-muted">
                                            <i class="fas fa-briefcase me-1"></i>{{ $currentParcours->poste }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Parcours History - Timeline Design -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i>Historique du Parcours
                </h5>
                <span class="badge bg-primary">{{ $parcours->unique('id')->count() }} parcours</span>
            </div>
        </div>
        <div class="card-body p-4">
            @php
                // Remove duplicates by id to prevent showing the same parcours multiple times
                $uniqueParcours = $parcours->unique('id')->values();
            @endphp
            @if($uniqueParcours->count() > 0)
                <div class="timeline">
                    @foreach($uniqueParcours as $index => $parcour)
                        @php
                            $isActive = $parcour->date_fin === null || $parcour->date_fin >= now();
                            $isChefParcours = $parcour->entite && $parcour->entite->chef_ppr === $parcour->ppr;
                            $isLast = $index === $uniqueParcours->count() - 1;
                        @endphp
                        <div class="timeline-item {{ $isLast ? 'timeline-item-last' : '' }}">
                            <div class="timeline-marker">
                                <div class="timeline-dot {{ $isActive ? 'timeline-dot-active' : 'timeline-dot-inactive' }}">
                                    @if($isActive)
                                        <i class="fas fa-circle"></i>
                                    @else
                                        <i class="far fa-circle"></i>
                                    @endif
                                </div>
                                @if(!$isLast)
                                    <div class="timeline-line"></div>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <div class="card border-0 shadow-sm h-100 parcours-card {{ $isActive ? 'parcours-card-active' : '' }}" style="transition: all 0.3s ease;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <h5 class="mb-0 fw-bold">
                                                        {{ $parcour->poste ?? 'Poste non spécifié' }}
                                                    </h5>
                                                    @if($isChefParcours)
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-crown me-1"></i>Chef
                                                        </span>
                                                    @endif
                                                    @if($isActive)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Actif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Terminé</span>
                                                    @endif
                                                </div>
                                                <p class="text-primary fw-semibold mb-0">
                                                    <i class="fas fa-building me-2"></i>{{ $parcour->entite->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <div class="info-box">
                                                    <div class="d-flex align-items-start">
                                                        <div class="info-icon bg-primary bg-opacity-10 text-primary">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <small class="text-muted d-block mb-1">Date de début</small>
                                                            <span class="fw-semibold">{{ $parcour->date_debut ? $parcour->date_debut->format('d/m/Y') : 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-box">
                                                    <div class="d-flex align-items-start">
                                                        <div class="info-icon bg-success bg-opacity-10 text-success">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <small class="text-muted d-block mb-1">Date de fin</small>
                                                            @if($parcour->date_fin)
                                                                <span class="fw-semibold">{{ $parcour->date_fin->format('d/m/Y') }}</span>
                                                            @else
                                                                <span class="badge bg-success">En cours</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($parcour->grade)
                                            <div class="mb-3">
                                                <div class="info-box">
                                                    <div class="d-flex align-items-start">
                                                        <div class="info-icon bg-info bg-opacity-10 text-info">
                                                            <i class="fas fa-user-tie"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <small class="text-muted d-block mb-1">Grade</small>
                                                            <span class="badge bg-info">{{ $parcour->grade->name ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($parcour->reason)
                                            <div class="mb-0">
                                                <div class="info-box">
                                                    <div class="d-flex align-items-start">
                                                        <div class="info-icon bg-warning bg-opacity-10 text-warning">
                                                            <i class="fas fa-info-circle"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <small class="text-muted d-block mb-1">Raison</small>
                                                            <span class="fw-semibold">{{ $parcour->reason }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-route fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">Aucun parcours enregistré</h5>
                    <p class="text-muted small">Votre parcours professionnel sera affiché ici</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-item-last {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -2rem;
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .timeline-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .timeline-dot-active {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: 3px solid white;
    }

    .timeline-dot-inactive {
        background: #6c757d;
        color: white;
        border: 3px solid white;
    }

    .timeline-line {
        width: 2px;
        flex-grow: 1;
        background: linear-gradient(180deg, #0d6efd 0%, #e9ecef 100%);
        margin-top: 0.5rem;
        min-height: calc(100% + 2rem);
    }

    .timeline-content {
        margin-left: 1rem;
    }

    /* Parcours Card Styles */
    .parcours-card {
        border-left: 4px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .parcours-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .parcours-card-active {
        border-left-color: #28a745;
        background: linear-gradient(90deg, rgba(40, 167, 69, 0.05) 0%, rgba(255, 255, 255, 1) 4%);
    }

    /* Info Box Styles */
    .info-box {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .info-box:hover {
        background: #e9ecef;
        transform: translateX(4px);
    }

    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .timeline {
            padding-left: 1.5rem;
        }

        .timeline-marker {
            left: -1.5rem;
        }

        .timeline-dot {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }

        .timeline-content {
            margin-left: 0.5rem;
        }
    }
</style>
@endpush
@endsection

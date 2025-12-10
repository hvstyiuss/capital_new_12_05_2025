@extends('layouts.app')

@section('title', 'Mon Parcours')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold text-dark">Mon Parcours</h1>
        <p class="text-muted mb-0">Votre parcours professionnel dans l'agence</p>
    </div>

    <!-- Profile Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        @if($user->userInfo && $user->userInfo->photo)
                            <img src="{{ asset('storage/' . $user->userInfo->photo) }}" 
                                 alt="{{ $user->fname }} {{ $user->lname }}" 
                                 class="rounded-circle me-3" 
                                 style="width: 70px; height: 70px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" 
                                 style="width: 70px; height: 70px;">
                                <span class="text-primary fw-bold" style="font-size: 1.8rem;">
                                    {{ strtoupper(substr($user->fname ?? 'N', 0, 1)) }}{{ strtoupper(substr($user->lname ?? 'C', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $user->fname }} {{ $user->lname }}</h4>
                            <p class="text-muted mb-1">
                                <strong>PPR:</strong> {{ $user->ppr }}
                                @if($user->email)
                                    <span class="ms-2">{{ $user->email }}</span>
                                @endif
                            </p>
                            @if($currentParcours)
                                <p class="mb-0">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-building me-1"></i>Actuellement: {{ $currentParcours->entite->name ?? 'N/A' }}
                                    </span>
                                    @php
                                        $isCurrentChef = $currentParcours->entite && $currentParcours->entite->chef_ppr === $currentParcours->ppr;
                                    @endphp
                                    @if($isCurrentChef)
                                        <span class="badge bg-warning text-dark ms-2">Chef</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center py-3">
                            <div class="text-muted small mb-1">Total Parcours</div>
                            <h2 class="mb-0 fw-bold text-primary">{{ $parcours->unique('id')->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parcours History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-history me-2"></i>Historique du Parcours
            </h5>
        </div>
        <div class="card-body p-4">
            @php
                // Remove duplicates by id to prevent showing the same parcours multiple times
                $uniqueParcours = $parcours->unique('id')->values();
            @endphp
            @if($uniqueParcours->count() > 0)
                @foreach($uniqueParcours as $index => $parcour)
                    @php
                        $isActive = $parcour->date_fin === null || $parcour->date_fin >= now();
                        $isChefParcours = $parcour->entite && $parcour->entite->chef_ppr === $parcour->ppr;
                    @endphp
                    <div class="border-start border-primary border-3 ps-4 mb-4 position-relative {{ $index === $uniqueParcours->count() - 1 ? 'mb-0' : '' }}">
                        @if($index < $uniqueParcours->count() - 1)
                            <div class="position-absolute start-0 top-0" style="width: 12px; height: 12px; background: #0d6efd; border-radius: 50%; transform: translateX(-50%); margin-top: -6px;"></div>
                        @endif
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-bold">
                                    {{ $parcour->poste ?? 'Poste non spécifié' }}
                                    @if($isChefParcours)
                                        <span class="badge bg-warning text-dark ms-2">
                                            <i class="fas fa-crown me-1"></i>Chef
                                        </span>
                                    @endif
                                </h5>
                                <p class="mb-2 text-primary fw-semibold">
                                    <i class="fas fa-building me-2"></i>{{ $parcour->entite->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                @if($isActive)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Terminé</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar-alt text-muted me-2 mt-1"></i>
                                    <div>
                                        <small class="text-muted d-block mb-1">Date de début</small>
                                        <span class="fw-semibold">{{ $parcour->date_debut ? $parcour->date_debut->format('d/m/Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar-check text-muted me-2 mt-1"></i>
                                    <div>
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

                        @if($parcour->grade)
                            <div class="mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-user-tie text-muted me-2 mt-1"></i>
                                    <div>
                                        <small class="text-muted d-block mb-1">Grade</small>
                                        <span class="badge bg-info">{{ $parcour->grade->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($parcour->reason)
                            <div class="mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle text-muted me-2 mt-1"></i>
                                    <div>
                                        <small class="text-muted d-block mb-1">Raison</small>
                                        <span class="fw-semibold">{{ $parcour->reason }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun parcours enregistré</h5>
                    <p class="text-muted small">Votre parcours professionnel sera affiché ici</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

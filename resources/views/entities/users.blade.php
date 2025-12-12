@extends('layouts.app')

@section('title', 'Personnes de l\'entité')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('entities.index') }}">Entités</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Personnes</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Personnes de l'entité
                </h1>
                <p class="text-muted mb-0">{{ $entite->name }}</p>
            </div>
            <div>
                <a href="{{ route('entities.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour aux entités
                </a>
            </div>
        </div>
    </div>

    <!-- Entity Info Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2 fw-semibold">
                        <i class="fas fa-building me-2 text-primary"></i>
                        {{ $entite->name }}
                    </h5>
                    @if($entite->entiteInfo && $entite->entiteInfo->description)
                        <p class="text-muted mb-0">{{ Str::limit($entite->entiteInfo->description, 150) }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    @if($entite->entiteInfo && $entite->entiteInfo->type)
                        @if($entite->entiteInfo->type == 'central')
                            <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">Central</span>
                        @elseif($entite->entiteInfo->type == 'regional')
                            <span class="badge bg-info rounded-pill fs-6 px-3 py-2">Régional</span>
                        @endif
                    @endif
                    <div class="mt-2">
                        <span class="badge bg-success rounded-pill fs-6 px-3 py-2">
                            <i class="fas fa-users me-1"></i>
                            {{ $activeParcours->count() }} personne(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List -->
    @if($activeParcours->count() > 0)
        <div class="row g-4">
            @foreach($activeParcours as $parcours)
                @php
                    $user = $parcours->user;
                    $fullName = $user->fname . ' ' . $user->lname;
                    $avatar = $user->userInfo && $user->userInfo->photo_url 
                        ? $user->userInfo->photo_url 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=0d6efd&color=fff&size=128';
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100 user-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <img src="{{ $avatar }}" 
                                     alt="{{ $fullName }}" 
                                     class="user-avatar me-3"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($fullName) }}&background=0d6efd&color=fff&size=128'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold text-dark">
                                        {{ $fullName }}
                                        @php
                                            $isChefParcours = $parcours->entite && $parcours->entite->chef_ppr === $parcours->ppr;
                                        @endphp
                                        @if($isChefParcours)
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="fas fa-crown me-1"></i>Chef
                                            </span>
                                        @endif
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <span class="badge bg-secondary">{{ $user->ppr }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            @if($parcours->poste)
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-briefcase me-1"></i>Poste
                                    </small>
                                    <p class="mb-0 fw-medium">{{ $parcours->poste }}</p>
                                </div>
                            @endif
                            
                            @if($parcours->grade)
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-graduation-cap me-1"></i>Grade
                                    </small>
                                    <p class="mb-0">{{ $parcours->grade->name }}</p>
                                </div>
                            @endif
                            
                            @if($parcours->date_debut)
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-calendar-alt me-1"></i>Date de début
                                    </small>
                                    <p class="mb-0">{{ $parcours->date_debut->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            
                            @if($user->email)
                                <div class="mt-3 pt-3 border-top">
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1 text-primary"></i>
                                        <small>{{ $user->email }}</small>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-slash fa-4x text-muted mb-4"></i>
                <h5 class="text-muted mb-3">Aucune personne</h5>
                <p class="text-muted mb-0">Aucune personne n'appartient actuellement à cette entité.</p>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .user-card {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e9ecef;
        flex-shrink: 0;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }

    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #0d6efd;
    }

    .card {
        border-radius: 0.5rem;
    }
</style>
@endpush
@endsection





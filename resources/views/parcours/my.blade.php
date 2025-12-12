@extends('layouts.app')

@section('title', 'Mon Parcours')

@section('content')
<div class="container-fluid py-4">
    <!-- Profile Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="parcours-profile-card">
                <div class="parcours-profile-header">
                    <div class="parcours-profile-avatar-section">
                        <div class="parcours-profile-avatar-wrapper">
                            @if($user->userInfo && $user->userInfo->photo_url)
                                <img src="{{ $user->userInfo->photo_url }}" 
                                     alt="{{ $user->fname }} {{ $user->lname }}" 
                                     class="parcours-profile-avatar">
                            @else
                                <div class="parcours-profile-avatar-placeholder">
                                    <span class="parcours-profile-initials">
                                        {{ strtoupper(substr($user->fname ?? 'N', 0, 1)) }}{{ strtoupper(substr($user->lname ?? 'C', 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            @if($currentParcours && $isCurrentChef)
                                    <div class="parcours-chef-badge">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="parcours-profile-info">
                        <h1 class="parcours-profile-name">{{ $user->fname }} {{ $user->lname }}</h1>
                        <div class="parcours-profile-details">
                            <div class="parcours-profile-detail-item">
                                <i class="fas fa-id-card"></i>
                                <span>PPR: <strong>{{ $user->ppr }}</strong></span>
                            </div>
                            @if($user->email)
                            <div class="parcours-profile-detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="parcours-profile-stats">
                        <div class="parcours-stat-box">
                            <div class="parcours-stat-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="parcours-stat-info">
                                <div class="parcours-stat-label">Total Parcours</div>
                                <div class="parcours-stat-value">{{ $parcours->unique('id')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Assignment Card -->
    @if($currentParcours)
    <div class="row mb-4">
        <div class="col-12">
            <div class="parcours-current-assignment-card">
                <div class="parcours-current-assignment-header">
                    <div class="parcours-current-assignment-icon-wrapper">
                        <div class="parcours-current-assignment-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                    <div class="parcours-current-assignment-content">
                        <h2 class="parcours-current-assignment-title">Affectation Actuelle</h2>
                        <p class="parcours-current-assignment-entity">{{ $currentParcours->entite->name ?? 'N/A' }}</p>
                    </div>
                    <div class="parcours-current-assignment-badge">
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i>Actif
                        </span>
                    </div>
                </div>
                <div class="parcours-current-assignment-body">
                    <div class="row g-3">
                        @if($currentParcours->poste)
                        <div class="col-md-4">
                            <div class="parcours-assignment-info-box">
                                <div class="parcours-assignment-info-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="parcours-assignment-info-content">
                                    <div class="parcours-assignment-info-label">Fonction</div>
                                    <div class="parcours-assignment-info-value">{{ $currentParcours->poste }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($currentParcours->grade)
                        <div class="col-md-4">
                            <div class="parcours-assignment-info-box">
                                <div class="parcours-assignment-info-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="parcours-assignment-info-content">
                                    <div class="parcours-assignment-info-label">Grade</div>
                                    <div class="parcours-assignment-info-value">{{ $currentParcours->grade->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="parcours-assignment-info-box">
                                <div class="parcours-assignment-info-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="parcours-assignment-info-content">
                                    <div class="parcours-assignment-info-label">Depuis le</div>
                                    <div class="parcours-assignment-info-value">{{ $currentParcours->date_debut ? $currentParcours->date_debut->format('d/m/Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Parcours History -->
    <div class="row">
        <div class="col-12">
            <div class="parcours-history-section">
                <div class="parcours-history-header">
                    <div class="parcours-history-header-content">
                        <div class="parcours-history-icon-wrapper">
                            <div class="parcours-history-icon">
                                <i class="fas fa-history"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="parcours-history-title">Historique du Parcours</h2>
                            <p class="parcours-history-subtitle">Votre évolution professionnelle dans l'agence</p>
                        </div>
                    </div>
                    <div class="parcours-history-badge">
                        <span class="badge bg-primary px-3 py-2">{{ $parcours->unique('id')->count() }} parcours</span>
                    </div>
                </div>
                <div class="parcours-history-content">
                    @if($uniqueParcours->count() > 0)
                        <div class="parcours-timeline-container">
                            @foreach($uniqueParcours as $index => $parcour)
                                <div class="parcours-timeline-entry {{ $parcour->isLast ? 'parcours-timeline-entry-last' : '' }}">
                                    <div class="parcours-timeline-marker-wrapper">
                                        <div class="parcours-timeline-dot-wrapper">
                                            <div class="parcours-timeline-dot {{ $parcour->isActive ? 'parcours-timeline-dot-active' : 'parcours-timeline-dot-inactive' }}">
                                                @if($parcour->isActive)
                                                    <i class="fas fa-circle"></i>
                                                @else
                                                    <i class="far fa-circle"></i>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!$parcour->isLast)
                                            <div class="parcours-timeline-connector"></div>
                                        @endif
                                    </div>
                                    <div class="parcours-timeline-card-wrapper">
                                        <div class="parcours-timeline-card {{ $parcour->isActive ? 'parcours-timeline-card-active' : '' }}">
                                            <div class="parcours-timeline-card-header">
                                                <div class="parcours-timeline-card-title-section">
                                                    <h3 class="parcours-timeline-card-title">
                                                        {{ $parcour->poste ?? 'Poste non spécifié' }}
                                                    </h3>
                                                    <p class="parcours-timeline-card-entity">
                                                        <i class="fas fa-building me-2"></i>{{ $parcour->entite->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="parcours-timeline-card-badges">
                                                    @if($parcour->isChefParcours)
                                                        <span class="badge bg-warning text-dark me-2">
                                                            <i class="fas fa-crown me-1"></i>Chef
                                                        </span>
                                                    @endif
                                                    @if($parcour->isActive)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Actif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Terminé</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="parcours-timeline-card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="parcours-detail-box">
                                                            <div class="parcours-detail-icon">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </div>
                                                            <div class="parcours-detail-content">
                                                                <div class="parcours-detail-label">Date de début</div>
                                                                <div class="parcours-detail-value">{{ $parcour->date_debut ? $parcour->date_debut->format('d/m/Y') : 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="parcours-detail-box">
                                                            <div class="parcours-detail-icon">
                                                                <i class="fas fa-calendar-check"></i>
                                                            </div>
                                                            <div class="parcours-detail-content">
                                                                <div class="parcours-detail-label">Date de fin</div>
                                                                <div class="parcours-detail-value">
                                                                    @if($parcour->date_fin)
                                                                        {{ $parcour->date_fin->format('d/m/Y') }}
                                                                    @else
                                                                        <span class="badge bg-success">En cours</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if($parcour->grade)
                                                    <div class="col-md-6">
                                                        <div class="parcours-detail-box">
                                                            <div class="parcours-detail-icon">
                                                                <i class="fas fa-user-tie"></i>
                                                            </div>
                                                            <div class="parcours-detail-content">
                                                                <div class="parcours-detail-label">Grade</div>
                                                                <div class="parcours-detail-value">
                                                                    <span class="badge bg-primary">{{ $parcour->grade->name ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if($parcour->reason)
                                                    <div class="col-md-6">
                                                        <div class="parcours-detail-box">
                                                            <div class="parcours-detail-icon">
                                                                <i class="fas fa-info-circle"></i>
                                                            </div>
                                                            <div class="parcours-detail-content">
                                                                <div class="parcours-detail-label">Raison</div>
                                                                <div class="parcours-detail-value">{{ $parcour->reason }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="parcours-empty-state">
                            <div class="parcours-empty-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <h5 class="parcours-empty-title">Aucun parcours enregistré</h5>
                            <p class="parcours-empty-text">Votre parcours professionnel sera affiché ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Profile Card */
    .parcours-profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .parcours-profile-header {
        background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
        padding: 2.5rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .parcours-profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .parcours-profile-avatar-section {
        position: relative;
        z-index: 1;
    }

    .parcours-profile-avatar-wrapper {
        position: relative;
    }

    .parcours-profile-avatar,
    .parcours-profile-avatar-placeholder {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .parcours-profile-avatar-placeholder {
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .parcours-profile-initials {
        font-size: 2.2rem;
        font-weight: bold;
        color: white;
    }

    .parcours-chef-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 38px;
        height: 38px;
        background: #ffc107;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        color: #212529;
        font-size: 1rem;
    }

    .parcours-profile-info {
        flex-grow: 1;
        position: relative;
        z-index: 1;
    }

    .parcours-profile-name {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: white;
    }

    .parcours-profile-details {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .parcours-profile-detail-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1rem;
        opacity: 0.95;
    }

    .parcours-profile-detail-item i {
        width: 20px;
        text-align: center;
    }

    .parcours-profile-stats {
        position: relative;
        z-index: 1;
    }

    .parcours-stat-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        min-width: 160px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .parcours-stat-icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }

    .parcours-stat-label {
        font-size: 0.875rem;
        opacity: 0.85;
        margin-bottom: 0.25rem;
    }

    .parcours-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
    }

    /* Current Assignment Card */
    .parcours-current-assignment-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .parcours-current-assignment-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .parcours-current-assignment-icon-wrapper {
        flex-shrink: 0;
    }

    .parcours-current-assignment-icon {
        width: 55px;
        height: 55px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #2c5530;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .parcours-current-assignment-content {
        flex-grow: 1;
    }

    .parcours-current-assignment-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #212529;
    }

    .parcours-current-assignment-entity {
        font-size: 1.1rem;
        color: #495057;
        margin-bottom: 0;
        font-weight: 500;
    }

    .parcours-current-assignment-body {
        padding: 1.75rem;
    }

    .parcours-assignment-info-box {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 1px solid #e9ecef;
    }

    .parcours-assignment-info-box:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .parcours-assignment-info-icon {
        width: 48px;
        height: 48px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2c5530;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .parcours-assignment-info-content {
        flex-grow: 1;
    }

    .parcours-assignment-info-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .parcours-assignment-info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #212529;
    }

    /* History Section */
    .parcours-history-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .parcours-history-header {
        background: #f8f9fa;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e9ecef;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .parcours-history-header-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .parcours-history-icon-wrapper {
        flex-shrink: 0;
    }

    .parcours-history-icon {
        width: 65px;
        height: 65px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        box-shadow: 0 4px 15px rgba(44, 85, 48, 0.3);
    }

    .parcours-history-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: #212529;
    }

    .parcours-history-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .parcours-history-content {
        padding: 2rem;
    }

    /* Timeline */
    .parcours-timeline-container {
        position: relative;
        padding-left: 3.5rem;
    }

    .parcours-timeline-entry {
        position: relative;
        margin-bottom: 2.5rem;
    }

    .parcours-timeline-entry-last {
        margin-bottom: 0;
    }

    .parcours-timeline-marker-wrapper {
        position: absolute;
        left: -3.5rem;
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .parcours-timeline-dot-wrapper {
        position: relative;
        z-index: 2;
    }

    .parcours-timeline-dot {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .parcours-timeline-dot-active {
        color: white;
    }

    .parcours-timeline-dot-inactive {
        background: #6c757d;
        color: white;
    }

    .parcours-timeline-connector {
        width: 3px;
        flex-grow: 1;
        margin-top: 0.5rem;
        min-height: calc(100% + 2.5rem);
        border-radius: 2px;
    }

    .parcours-timeline-card-wrapper {
        margin-left: 1rem;
    }

    .parcours-timeline-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .parcours-timeline-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .parcours-timeline-card-active {
        border-color: #2c5530;
        box-shadow: 0 4px 15px rgba(44, 85, 48, 0.15);
    }

    .parcours-timeline-card-header {
        background: #f8f9fa;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .parcours-timeline-card-title-section {
        flex-grow: 1;
    }

    .parcours-timeline-card-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #212529;
    }

    .parcours-timeline-card-entity {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .parcours-timeline-card-badges {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .parcours-timeline-card-body {
        padding: 1.5rem;
    }

    .parcours-detail-box {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 1px solid #e9ecef;
    }

    .parcours-detail-box:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .parcours-detail-icon {
        width: 48px;
        height: 48px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2c5530;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .parcours-detail-content {
        flex-grow: 1;
    }

    .parcours-detail-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .parcours-detail-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #212529;
    }

    /* Empty State */
    .parcours-empty-state {
        text-align: center;
        padding: 5rem 2rem;
    }

    .parcours-empty-icon {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }

    .parcours-empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .parcours-empty-text {
        color: #adb5bd;
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .parcours-profile-header {
            padding: 1.5rem;
            flex-direction: column;
            text-align: center;
        }

        .parcours-profile-name {
            font-size: 1.5rem;
        }

        .parcours-profile-avatar,
        .parcours-profile-avatar-placeholder {
            width: 90px;
            height: 90px;
        }

        .parcours-profile-initials {
            font-size: 1.75rem;
        }

        .parcours-timeline-container {
            padding-left: 2.5rem;
        }

        .parcours-timeline-marker-wrapper {
            left: -2.5rem;
        }

        .parcours-timeline-dot {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }

        .parcours-history-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .parcours-current-assignment-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Affectation')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Affectation</h1>
            <p class="text-muted mb-0">Historique des transferts et affectations</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-check text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Affectations</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalAffectations) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Ce Mois</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($thisMonthAffectations) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('affectation.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label small text-muted">Rechercher</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               placeholder="Nom, PPR, poste, entité..." 
                               value="{{ request('search') }}"
                               autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label for="entite_id" class="form-label small text-muted">Entité</label>
                        <select class="form-select" id="entite_id" name="entite_id">
                            <option value="">Toutes les entités</option>
                            @foreach($entites as $entite)
                                <option value="{{ $entite->id }}" {{ request('entite_id') == $entite->id ? 'selected' : '' }}>
                                    {{ $entite->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label small text-muted">Date début</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label small text-muted">Date fin</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list me-2"></i>Liste des Affectations
            </h5>
        </div>
        <div class="card-body p-0">
            @if($affectations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">#</th>
                                <th class="px-3 py-3">Utilisateur</th>
                                <th class="px-3 py-3">Entité de Destination</th>
                                <th class="px-3 py-3">Poste</th>
                                <th class="px-3 py-3">Date de Début</th>
                                <th class="px-3 py-3">Raison</th>
                                <th class="px-3 py-3">Effectué par</th>
                                <th class="px-3 py-3">Date d'Affectation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($affectations as $index => $affectation)
                                <tr>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-secondary">{{ $affectations->firstItem() + $index }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($affectation->user && $affectation->user->userInfo && $affectation->user->userInfo->photo)
                                                <img src="{{ $affectation->user->userInfo->photo_url }}" 
                                                     alt="{{ $affectation->user->fname }} {{ $affectation->user->lname }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    <span class="text-primary fw-bold small">
                                                        {{ strtoupper(substr($affectation->user->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr($affectation->user->lname ?? '', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $affectation->user->fname ?? 'N/A' }} {{ $affectation->user->lname ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $affectation->ppr }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-primary fw-semibold">
                                            <i class="fas fa-building me-1"></i>{{ $affectation->entite->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-info">{{ $affectation->poste ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        {{ $affectation->date_debut ? $affectation->date_debut->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $affectation->reason ?? 'N/A' }}</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($affectation->createdBy)
                                            <div>
                                                <div class="fw-semibold small">{{ $affectation->createdBy->fname }} {{ $affectation->createdBy->lname }}</div>
                                                <small class="text-muted">{{ $affectation->created_by_ppr }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">
                                            {{ $affectation->created_at ? $affectation->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $affectations->firstItem() }} à {{ $affectations->lastItem() }} sur {{ $affectations->total() }} affectation(s)
                        </div>
                        <div>
                            {{ $affectations->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-user-check fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucune affectation trouvée</h5>
                    <p class="text-muted small">Aucun transfert n'a été effectué pour le moment</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection







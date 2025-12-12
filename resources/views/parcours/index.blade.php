@extends('layouts.app')

@section('title', 'Parcours Professionnels')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div>
                <h1 class="text-4xl font-bold bg-clip-text">
                    Parcours Professionnels
                </h1>
                <p class="text-gray-600 text-lg mt-2">Gestion et suivi des parcours professionnels des utilisateurs</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-6">
        <div class="col-md-3">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-route text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-0 small fw-semibold">Total Parcours</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalParcours) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-0 small fw-semibold">Parcours Actifs</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($activeParcours) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-crown text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-0 small fw-semibold">Chefs Actifs</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($chefParcours) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-0 small fw-semibold">Utilisateurs</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalUsers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20 mb-6">
        <form method="GET" action="{{ route('parcours.index') }}" id="filterForm" class="row align-items-end">
            <div class="col-md-4 mb-3">
                <label for="search" class="form-label fw-semibold">
                    <i class="fas fa-search me-2 text-primary"></i>Rechercher
                </label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       placeholder="Nom, PPR, poste, entité..." 
                       value="{{ request('search') }}"
                       autocomplete="off">
            </div>
            <div class="col-md-3 mb-3">
                <label for="entite" class="form-label fw-semibold">
                    <i class="fas fa-building me-2 text-primary"></i>Entité
                </label>
                <select class="form-select" id="entite" name="entite">
                    <option value="">Toutes les entités</option>
                    @foreach($entites as $entite)
                        <option value="{{ $entite->id }}" {{ request('entite') == $entite->id ? 'selected' : '' }}>
                            {{ $entite->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="status" class="form-label fw-semibold">
                    <i class="fas fa-filter me-2 text-primary"></i>Statut
                </label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
            </div>
            @if(request()->has('search') || request()->has('entite') || request()->has('status'))
                <div class="col-12">
                    <a href="{{ route('parcours.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Parcours Table -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20">
        <div class="p-6">
            @if($parcours->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="fw-semibold">
                                    <i class="fas fa-user me-2 text-primary"></i>Utilisateur
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-briefcase me-2 text-primary"></i>Poste
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-building me-2 text-primary"></i>Entité
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-user-tie me-2 text-primary"></i>Grade
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Date Début
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i>Date Fin
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Statut
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-crown me-2 text-primary"></i>Rôle
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parcours as $parcour)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($parcour->user && $parcour->user->userInfo && $parcour->user->userInfo->photo)
                                                <img src="{{ $parcour->user->userInfo->photo_url }}" 
                                                     alt="{{ $parcour->user->fname }} {{ $parcour->user->lname }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-primary fw-bold">
                                                        {{ $parcour->user->initials ?? 'U' }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $parcour->user->fname ?? 'N/A' }} {{ $parcour->user->lname ?? '' }}</div>
                                                <small class="text-muted">{{ $parcour->ppr }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $parcour->poste ?? 'N/A' }}</span>
                                        @if($parcour->reason)
                                            <br><small class="text-muted">{{ $parcour->reason }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $parcour->entite->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $parcour->grade->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            {{ $parcour->date_debut_formatted }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($parcour->date_fin_formatted)
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                                {{ $parcour->date_fin_formatted }}
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">En cours</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($parcour->isActive)
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($parcour->isChefParcours)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-crown me-1"></i>Chef
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">Collaborateur</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-2"></i>
                        Affichage de {{ $parcours->firstItem() }} à {{ $parcours->lastItem() }} sur {{ $parcours->total() }} parcours
                    </div>
                    <div>
                        {{ $parcours->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="mb-4">
                        <i class="fas fa-route text-gray-400" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-gray-600 mb-2">Aucun parcours trouvé</h3>
                    <p class="text-gray-500">
                        @if(request()->has('search') || request()->has('entite') || request()->has('status'))
                            Aucun parcours ne correspond à vos critères de recherche.
                        @else
                            Aucun parcours professionnel n'est enregistré.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    const entiteSelect = document.getElementById('entite');
    const statusSelect = document.getElementById('status');

    // Debounce function for search
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500);
    });

    // Auto-submit on filter change
    [entiteSelect, statusSelect].forEach(function(element) {
        element.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Reset filters function
    window.resetFilters = function() {
        searchInput.value = '';
        entiteSelect.value = '';
        statusSelect.value = '';
        filterForm.submit();
    };
});
</script>
@endpush
@endsection

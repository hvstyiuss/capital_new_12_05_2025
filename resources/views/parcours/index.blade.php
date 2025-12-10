@extends('layouts.app')

@section('title', 'Parcours Professionnels')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Parcours Professionnels</h1>
            <p class="text-muted mb-0">Gestion et suivi des parcours professionnels des utilisateurs</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-route text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Parcours</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalParcours) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Parcours Actifs</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($activeParcours) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-crown text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Chefs Actifs</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($chefParcours) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Utilisateurs</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalUsers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('parcours.index') }}" id="filterForm">
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
                        <label for="entite" class="form-label small text-muted">Entité</label>
                        <select class="form-select" id="entite" name="entite">
                            <option value="">Toutes les entités</option>
                            @foreach($entites as $entite)
                                <option value="{{ $entite->id }}" {{ request('entite') == $entite->id ? 'selected' : '' }}>
                                    {{ $entite->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label small text-muted">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <!-- Rôle filter removed: chef status is now stored on entites.chef_ppr -->
                </div>
            </form>
        </div>
    </div>

    <!-- Parcours Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($parcours->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Utilisateur</th>
                                <th class="border-0">Poste</th>
                                <th class="border-0">Entité</th>
                                <th class="border-0">Grade</th>
                                <th class="border-0">Date Début</th>
                                <th class="border-0">Date Fin</th>
                                <th class="border-0">Statut</th>
                                <th class="border-0">Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parcours as $parcour)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($parcour->user && $parcour->user->userInfo && $parcour->user->userInfo->photo)
                                                <img src="{{ asset('storage/' . $parcour->user->userInfo->photo) }}" 
                                                     alt="{{ $parcour->user->fname }} {{ $parcour->user->lname }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-primary fw-bold">
                                                        {{ strtoupper(substr($parcour->user->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr($parcour->user->lname ?? '', 0, 1)) }}
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
                                        <span class="text-dark">{{ $parcour->date_debut ? $parcour->date_debut->format('d/m/Y') : 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($parcour->date_fin)
                                            <span class="text-dark">{{ $parcour->date_fin->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-success">En cours</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $isActive = $parcour->date_fin === null || $parcour->date_fin >= now();
                                        @endphp
                                        @if($isActive)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $isChefParcours = $parcour->entite && $parcour->entite->chef_ppr === $parcour->ppr;
                                        @endphp
                                        @if($isChefParcours)
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
                        Affichage de {{ $parcours->firstItem() }} à {{ $parcours->lastItem() }} sur {{ $parcours->total() }} parcours
                    </div>
                    <div>
                        {{ $parcours->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-route fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucun parcours trouvé</h5>
                    <p class="text-muted small">Essayez de modifier vos critères de recherche</p>
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

@extends('layouts.app')

@section('title', 'Gestion des Types d\'Annonces')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-tags me-2 text-warning"></i>
                    Gestion des Types d'Annonces
                </h1>
                <p class="text-muted mb-0">Administration des types d'annonces</p>
            </div>
            <div>
                <a href="{{ route('type-annonces.create') }}" class="btn btn-warning">
                    <i class="fas fa-plus me-2"></i>Nouveau Type
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(255, 193, 7, 0.1);">
                                <i class="fas fa-tags fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Types</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $types->total() }}</h3>
                            <small class="text-muted">Types créés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(25, 135, 84, 0.1);">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Types Actifs</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $types->where('is_active', true)->count() }}</h3>
                            <small class="text-muted">En cours d'utilisation</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="fas fa-bullhorn fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Annonces</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $types->sum('annonces_count') }}</h3>
                            <small class="text-muted">Annonces associées</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-filter me-2 text-warning"></i>Recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('type-annonces.index') }}" id="searchForm" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">
                        <i class="fas fa-search me-1 text-muted"></i>Rechercher
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           class="form-control" 
                           placeholder="Rechercher par nom ou description..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="statut" class="form-label fw-semibold">
                        <i class="fas fa-toggle-on me-1 text-muted"></i>Statut
                    </label>
                    <select id="statut" name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="has_annonces" class="form-label fw-semibold">
                        <i class="fas fa-bullhorn me-1 text-muted"></i>Avec Annonces
                    </label>
                    <select id="has_annonces" name="has_annonces" class="form-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('has_annonces') == '1' ? 'selected' : '' }}>Avec annonces</option>
                        <option value="0" {{ request('has_annonces') == '0' ? 'selected' : '' }}>Sans annonces</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Types Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-table me-2 text-warning"></i>Liste des Types d'Annonces
                </h5>
                <span class="badge bg-warning rounded-pill">{{ $types->total() }} types trouvés</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Nom</th>
                            <th class="fw-semibold">Description</th>
                            <th class="fw-semibold">Couleur</th>
                            <th class="fw-semibold">Statut</th>
                            <th class="fw-semibold">Annonces</th>
                            <th class="fw-semibold">Date de création</th>
                            <th class="fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-tag text-warning"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $type->nom }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $type->description ? Str::limit($type->description, 50) : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($type->couleur)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle" 
                                                 style="width: 24px; height: 24px; background-color: {{ $type->couleur }}; border: 2px solid #dee2e6;"></div>
                                            <small class="text-muted font-monospace">{{ $type->couleur }}</small>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">
                                            <i class="fas fa-minus me-1"></i>Non définie
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->is_active)
                                        <span class="badge bg-success rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i>Actif
                                        </span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i>Inactif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill">
                                        <i class="fas fa-bullhorn me-1"></i>{{ $type->annonces_count }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ $type->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('type-annonces.show', $type) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('type-annonces.edit', $type) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('type-annonces.destroy', $type) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucun type trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($types->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Affichage de <strong>{{ $types->firstItem() }}</strong> à <strong>{{ $types->lastItem() }}</strong> 
                        sur <strong>{{ $types->total() }}</strong> résultat(s)
                    </div>
                    <div>
                        {{ $types->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit search form with debounce
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('search');
    const statutSelect = document.getElementById('statut');
    const hasAnnoncesSelect = document.getElementById('has_annonces');
    
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 500);
        });
    }
    
    if (statutSelect) {
        statutSelect.addEventListener('change', function() {
            searchForm.submit();
        });
    }
    
    if (hasAnnoncesSelect) {
        hasAnnoncesSelect.addEventListener('change', function() {
            searchForm.submit();
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.card {
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.table th {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm i {
    font-size: 0.875rem;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.font-monospace {
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
}
</style>
@endpush

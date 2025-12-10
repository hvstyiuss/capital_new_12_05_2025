@extends('layouts.app')

@section('title', 'Annonces')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-bullhorn me-2 text-info"></i>
                    Annonces
                </h1>
                <p class="text-muted mb-0">Consultez toutes les annonces publiées</p>
            </div>
            @if(auth()->check() && auth()->user()->hasRole('admin'))
            <div>
                <a href="{{ route('annonces.create') }}" class="btn btn-info">
                    <i class="fas fa-plus me-2"></i>Nouvelle Annonce
                </a>
            </div>
            @endif
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
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="fas fa-bullhorn fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Annonces</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $annonces->total() }}</h3>
                            <small class="text-muted">Annonces publiées</small>
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
                            <h6 class="text-muted mb-1 small">Annonces Actives</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $annonces->where('statut', 'active')->count() }}</h3>
                            <small class="text-muted">En cours</small>
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
                            <div class="rounded-circle p-3" style="background-color: rgba(108, 117, 125, 0.1);">
                                <i class="fas fa-pause-circle fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Annonces Inactives</h6>
                            <h3 class="mb-0 fw-bold text-secondary">{{ $annonces->where('statut', 'inactive')->count() }}</h3>
                            <small class="text-muted">Suspendues</small>
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
                <i class="fas fa-filter me-2 text-info"></i>Recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('annonces.index') }}" id="searchForm" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">
                        <i class="fas fa-search me-1 text-muted"></i>Rechercher
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           class="form-control" 
                           placeholder="Rechercher dans le contenu..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="statut" class="form-label fw-semibold">
                        <i class="fas fa-toggle-on me-1 text-muted"></i>Statut
                    </label>
                    <select id="statut" name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('statut') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label fw-semibold">
                        <i class="fas fa-tag me-1 text-muted"></i>Type
                    </label>
                    <select id="type" name="type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach(\App\Models\TypeAnnonce::where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                {{ $type->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Annonces List -->
    @if($annonces->count() > 0)
        <div class="row g-4">
            @foreach($annonces as $annonce)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 h-100 hover-shadow">
                    @if($annonce->image)
                        <img src="{{ Storage::url($annonce->image) }}" 
                             class="card-img-top" 
                             alt="Annonce image"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-gradient-to-br from-info to-primary d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-bullhorn fa-4x text-white opacity-50"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                @if($annonce->typeAnnonce)
                                    <span class="badge rounded-pill" 
                                          style="background-color: {{ $annonce->typeAnnonce->couleur ?: '#0dcaf0' }}; color: white;">
                                        <i class="fas fa-tag me-1"></i>{{ $annonce->typeAnnonce->nom }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">
                                        <i class="fas fa-tag me-1"></i>Annonce générale
                                    </span>
                                @endif
                                
                                @if($annonce->statut === 'active')
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">
                                        <i class="fas fa-pause-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-muted small mb-2">
                                <i class="fas fa-user me-1"></i>
                                {{ $annonce->user->fname ?? 'Utilisateur' }} {{ $annonce->user->lname ?? '' }}
                                @if($annonce->entites->count() > 0)
                                    <br>
                                    <i class="fas fa-building me-1"></i>
                                    {{ $annonce->entites->take(2)->pluck('name')->join(', ') }}
                                    @if($annonce->entites->count() > 2)
                                        +{{ $annonce->entites->count() - 2 }} autres
                                    @endif
                                @endif
                            </p>
                            
                            <p class="text-muted small mb-0">
                                <i class="far fa-clock me-1"></i>
                                {{ $annonce->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        
                        <div class="mb-3 flex-grow-1">
                            <p class="text-dark" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit($annonce->content, 150) }}
                            </p>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('annonces.show', $annonce) }}" 
                               class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye me-1"></i>Lire la suite
                            </a>
                            
                            @if(auth()->check() && auth()->user()->hasRole('admin'))
                            <div class="btn-group">
                                <a href="{{ route('annonces.edit', $annonce) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('annonces.destroy', $annonce) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($annonces->hasPages())
        <div class="mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Affichage de <strong>{{ $annonces->firstItem() }}</strong> à <strong>{{ $annonces->lastItem() }}</strong> 
                            sur <strong>{{ $annonces->total() }}</strong> résultat(s)
                        </div>
                        <div>
                            {{ $annonces->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-bullhorn fa-4x text-muted opacity-50"></i>
                </div>
                <h3 class="h5 fw-semibold text-gray-800 mb-2">Aucune annonce</h3>
                <p class="text-muted mb-4">Il n'y a pas d'annonces pour le moment.</p>
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                <a href="{{ route('annonces.create') }}" class="btn btn-info">
                    <i class="fas fa-plus me-2"></i>Créer la première annonce
                </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-submit search form with debounce
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('search');
    const statutSelect = document.getElementById('statut');
    const typeSelect = document.getElementById('type');
    
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
    
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
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

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}

.card-img-top {
    border-radius: 0.5rem 0.5rem 0 0;
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

.bg-gradient-to-br {
    background: linear-gradient(to bottom right, var(--bs-info), var(--bs-primary));
}
</style>
@endpush

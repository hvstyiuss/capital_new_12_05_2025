@extends('layouts.app')

@section('title', 'Consulter mes agents')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list me-2"></i>
                Consulter mes agents
            </h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('agents.consulter') }}" id="filterForm" class="row align-items-end">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="search" class="form-label mb-1">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nom, PPR ou email..."
                               autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="status" class="form-label mb-1">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    @if(request('search') || request('status'))
                    <a href="{{ route('agents.consulter') }}" class="btn btn-outline-secondary" title="Réinitialiser les filtres">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
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

    <!-- Agents Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>PPR</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Entité</th>
                            <th>Poste</th>
                            <th>Chef</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                            <tr>
                                <td>
                                    <strong>{{ $agent->ppr }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $agent->fname }} {{ $agent->lname }}</strong>
                                </td>
                                <td>{{ $agent->email ?? '-' }}</td>
                                <td>
                                    @if($agent->activeParcours && $agent->activeParcours->entite)
                                        {{ $agent->activeParcours->entite->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $agent->activeParcours ? ($agent->activeParcours->poste ?? '-') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($agent->isChef())
                                        <span class="badge bg-primary">
                                            <i class="fas fa-crown me-1"></i>Chef
                                        </span>
                                    @else
                                        <span class="text-muted">Collaborateur</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('parcours.show', $agent->ppr) }}" class="btn btn-sm " title="Voir le parcours">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun agent trouvé</p>
                                    @if(request('search') || request('status'))
                                        <a href="{{ route('agents.consulter') }}" class="btn btn-sm btn-outline-primary mt-2">
                                            Afficher tous les agents
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($agents->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-muted">
                                Affichage de {{ $agents->firstItem() }} à {{ $agents->lastItem() }} sur {{ $agents->total() }} agents
                            </p>
                        </div>
                        <div>
                            {{ $agents->appends(request()->query())->links() }}
                        </div>
                    </div>
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
    const statusSelect = document.getElementById('status');

    // Debounce function for search input
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                filterForm.submit();
            }, 500); // Wait 500ms after user stops typing
        });
    }

    // Auto-submit on status change
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }
});
</script>
@endpush
@endsection













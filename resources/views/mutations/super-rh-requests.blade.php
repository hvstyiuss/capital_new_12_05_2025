@extends('layouts.app')

@section('title', 'Suivi des demandes des personnelles')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Suivi des demandes des personnelles</h1>
            <p class="text-muted mb-0">Réviser les demandes de mutation externe avant envoi à la destination</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mutations.super-rh.requests') }}" id="filterForm" data-skip-loading="true">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label small text-muted">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Nom, PPR ou entité..." 
                                   value="{{ $search }}"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label small text-muted">Par page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($mutations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Agent</th>
                                <th class="border-0">Entité de Destination</th>
                                <th class="border-0">Direction Actuelle</th>
                                <th class="border-0">Direction Destination</th>
                                <th class="border-0 text-center">Statut</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mutations as $mutation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $mutation['user_name'] }}</div>
                                            <small class="text-muted">{{ $mutation['ppr'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $mutation['to_entite_name'] }}</div>
                                    <small class="text-muted">{{ $mutation['motif'] }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $mutation['current_direction_name'] }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $mutation['destination_direction_name'] }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i>{{ $mutation['statut'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('mutations.super-rh.validate', $mutation['id']) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Réviser la demande">
                                            <i class="fas fa-eye me-1"></i>Réviser
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="text-muted small">
                        Affichage de {{ $mutations->firstItem() ?? 0 }} à {{ $mutations->lastItem() ?? 0 }} sur {{ $mutations->total() }} demande(s)
                    </div>
                    <div>
                        {{ $mutations->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucune demande de mutation trouvée</h5>
                    <p class="text-muted small">Il n'y a pas de demandes de mutation externe en attente de révision</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const perPageSelect = document.getElementById('per_page');
    const searchInput = document.getElementById('search');

    // Auto-submit on filter change
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Debounce search input
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }
});
</script>
@endpush












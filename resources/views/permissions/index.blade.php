@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-key me-2 text-info"></i>
                    Gestion des Permissions
                </h1>
                <p class="text-muted mb-0">Administration des permissions système</p>
            </div>
            <div>
                <a href="{{ route('permissions.create') }}" class="btn btn-info">
                    <i class="fas fa-plus me-2"></i>Nouvelle Permission
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="fas fa-key fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Permissions</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $totalPermissions }}</h3>
                            <small class="text-muted">Permissions créées</small>
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
                                <i class="fas fa-shield-alt fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Rôles Associés</h6>
                            <h3 class="mb-0 fw-bold text-secondary">{{ $totalRoleAssociations }}</h3>
                            <small class="text-muted">Associations totales</small>
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
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 110, 253, 0.1);">
                                <i class="fas fa-link fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Permissions Actives</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $activePermissions }}</h3>
                            <small class="text-muted">Avec rôles assignés</small>
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
            <form method="GET" action="{{ route('permissions.index') }}" id="searchForm" class="row g-3">
                <div class="col-md-12">
                    <label for="search" class="form-label fw-semibold">
                        <i class="fas fa-search me-1 text-muted"></i>Rechercher
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           class="form-control" 
                           placeholder="Rechercher par nom de permission..."
                           value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-table me-2 text-info"></i>Liste des Permissions
                </h5>
                <span class="badge bg-info rounded-pill">{{ $permissions->total() }} permissions trouvées</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Nom</th>
                            <th class="fw-semibold">Rôles</th>
                            <th class="fw-semibold">Date de création</th>
                            <th class="fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-key text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $permission->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($permission->roles->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($permission->roles->take(3) as $role)
                                                <span class="badge bg-primary rounded-pill">
                                                    <i class="fas fa-shield-alt me-1"></i>{{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                            @if($permission->roles->count() > 3)
                                                <span class="badge bg-secondary rounded-pill">
                                                    +{{ $permission->roles->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">
                                            <i class="fas fa-minus me-1"></i>Aucun rôle
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ $permission->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('permissions.show', $permission) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('permissions.edit', $permission) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?');">
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
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucune permission trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($permissions->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Affichage de <strong>{{ $permissions->firstItem() }}</strong> à <strong>{{ $permissions->lastItem() }}</strong> 
                        sur <strong>{{ $permissions->total() }}</strong> résultat(s)
                    </div>
                    <div>
                        {{ $permissions->appends(request()->query())->links() }}
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
    
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 500);
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
    background-color: rgba(13, 202, 240, 0.1) !important;
}
</style>
@endpush

@extends('layouts.app')

@section('title', 'Demandes Mutations')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Demandes Mutations</h1>
            <p class="text-muted mb-0">Gérez les demandes de mutation</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mutations.agent-requests') }}" id="filterForm" data-skip-loading="true">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label small text-muted">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="" {{ $status === '' || $status === null ? 'selected' : '' }}>Tous les types</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En Attente</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvées</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejetées</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="mutation_type" class="form-label small text-muted">Type</label>
                        <select class="form-select" id="mutation_type" name="mutation_type">
                            <option value="">Tous les types</option>
                            <option value="interne" {{ $mutationType === 'interne' ? 'selected' : '' }}>Interne</option>
                            <option value="externe" {{ $mutationType === 'externe' ? 'selected' : '' }}>Externe</option>
                        </select>
                    </div>
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
                                <th class="border-0">Type</th>
                                <th class="border-0">Entité de Destination</th>
                                <th class="border-0">Direction Actuelle</th>
                                <th class="border-0">Direction Destination</th>
                                <th class="border-0 text-center">Approbations</th>
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
                                    @if($mutation['mutation_type'] === 'interne')
                                        <span class="badge bg-info">
                                            <i class="fas fa-arrow-right-arrow-left me-1"></i>Interne
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-external-link-alt me-1"></i>Externe
                                        </span>
                                    @endif
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
                                    <div class="d-flex flex-column gap-1">
                                        @if($mutation['mutation_type'] === 'interne')
                                            @if($mutation['approved_by_current_direction'])
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Approuvée
                                                </span>
                                            @elseif($mutation['rejected_by_current_direction'])
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Rejetée
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>En attente
                                                </span>
                                            @endif
                                        @else
                                            <div>
                                                @if($mutation['approved_by_current_direction'])
                                                    <span class="badge bg-success mb-1">
                                                        <i class="fas fa-check me-1"></i>Actuelle: OK
                                                    </span>
                                                @elseif($mutation['rejected_by_current_direction'])
                                                    <span class="badge bg-danger mb-1">
                                                        <i class="fas fa-times me-1"></i>Actuelle: Rejetée
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark mb-1">
                                                        <i class="fas fa-clock me-1"></i>Actuelle: En attente
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                @if($mutation['approved_by_destination_direction'])
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Destination: OK
                                                    </span>
                                                @elseif($mutation['rejected_by_destination_direction'])
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Destination: Rejetée
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i>Destination: En attente
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @if($mutation['can_approve_current'])
                                            <form action="{{ route('mutations.approve', $mutation['id']) }}" method="POST" class="d-inline mr-3 mutation-action-form" data-skip-loading="true">
                                                @csrf
                                                <input type="hidden" name="approval_type" value="current">
                                                <button type="submit" class="btn btn-sm btn-success mutation-action-btn" title="Approuver (Direction Actuelle)" data-mutation-id="{{ $mutation['id'] }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($mutation['can_approve_destination'])
                                            <form action="{{ route('mutations.approve', $mutation['id']) }}" method="POST" class="d-inline mutation-action-form" data-skip-loading="true">
                                                @csrf
                                                <input type="hidden" name="approval_type" value="destination">
                                                <button type="submit" class="btn btn-sm btn-success mutation-action-btn" title="Approuver (Direction Destination)" data-mutation-id="{{ $mutation['id'] }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($mutation['can_reject_current'])
                                            <form action="{{ route('mutations.reject', $mutation['id']) }}" method="POST" class="d-inline mutation-action-form" data-skip-loading="true">
                                                @csrf
                                                <input type="hidden" name="rejection_type" value="current">
                                                <input type="hidden" name="rejection_reason" value="">
                                                <button type="submit" class="btn btn-sm btn-danger mutation-action-btn" title="Rejeter (Direction Actuelle)" data-mutation-id="{{ $mutation['id'] }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($mutation['can_reject_destination'])
                                            <form action="{{ route('mutations.reject', $mutation['id']) }}" method="POST" class="d-inline mutation-action-form" data-skip-loading="true">
                                                @csrf
                                                <input type="hidden" name="rejection_type" value="destination">
                                                <input type="hidden" name="rejection_reason" value="">
                                                <button type="submit" class="btn btn-sm btn-danger mutation-action-btn" title="Rejeter (Direction Destination)" data-mutation-id="{{ $mutation['id'] }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
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
                    <p class="text-muted small">Il n'y a pas de demandes de mutation pour vos agents</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const statusSelect = document.getElementById('status');
    const mutationTypeSelect = document.getElementById('mutation_type');
    const perPageSelect = document.getElementById('per_page');
    const searchInput = document.getElementById('search');

    // Auto-submit on filter change
    [statusSelect, mutationTypeSelect, perPageSelect].forEach(function(element) {
        element.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Debounce search input
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500);
    });

    // Handle mutation action buttons (approve/reject) - prevent duplicate loading states
    document.querySelectorAll('.mutation-action-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('.mutation-action-btn');
            if (button && !button.hasAttribute('data-submitting')) {
                // Mark as submitting to prevent duplicate handlers
                button.setAttribute('data-submitting', 'true');
                button.disabled = true;
                
                // Store original icon class
                const icon = button.querySelector('i');
                if (icon) {
                    button.setAttribute('data-original-icon', icon.className);
                    icon.className = 'fas fa-spinner fa-spin';
                }
            }
        });
    });
});
</script>
@endpush
@endsection



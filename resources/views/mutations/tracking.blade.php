@extends('layouts.app')

@section('title', 'Suivi desMutation')

@push('styles')
<style>
    /* Force cleanup of stuck modals */
    body:not(.modal-open) .modal-backdrop {
        display: none !important;
        pointer-events: none !important;
    }
    
    /* Ensure page is clickable when no modal is open */
    body:not(.modal-open) {
        overflow: auto !important;
        pointer-events: auto !important;
    }
    
    /* Hide any hidden modals that might be blocking */
    .modal:not(.show) {
        display: none !important;
        pointer-events: none !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Suivi des Demandes de Mutation</h1>
            <p class="text-muted mb-0">Consultez l'état de vos demandes de mutation</p>
        </div>
        @if(isset($hasPendingMutation) && $hasPendingMutation)
            <button type="button" class="btn btn-primary" disabled title="Vous avez déjà une demande en attente. Vous ne pouvez pas créer une nouvelle demande tant que la précédente n'est pas traitée.">
                <i class="fas fa-lock me-2"></i>Faire une Demande
            </button>
        @else
            <a href="{{ route('mutations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Faire une Demande
            </a>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-exchange-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Demandes</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalMutations ?? $items->total()) }}</h4>
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
                                <i class="fas fa-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">En Attente</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($pendingMutations ?? 0) }}</h4>
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
                            <p class="text-muted mb-0 small">Validées</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($approvedMutations ?? 0) }}</h4>
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
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-times-circle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Rejetées</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($rejectedMutations ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mutations.tracking') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="year" class="form-label small text-muted">Année</label>
                        <select class="form-select" id="year" name="year">
                            @foreach($availableYears ?? [] as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="per_page" class="form-label small text-muted">Afficher</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 lignes par page</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 lignes par page</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 lignes par page</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 lignes par page</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label small text-muted">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Rechercher par entité..." 
                                   value="{{ $search }}"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 text-center" style="width: 60px;">Nº</th>
                                <th class="border-0">Entité de Destination</th>
                                <th class="border-0 text-center">Date de Dépôt</th>
                                <th class="border-0 text-center">Type</th>
                                <th class="border-0 text-center">Statut</th>
                                <th class="border-0 text-center">Validé par entité actuelle</th>
                                <th class="border-0 text-center">Validé par entité destination</th>
                                <th class="border-0">Décision Conducteur RH</th>
                                <th class="border-0">Validé par</th>
                                <th class="border-0 text-center">Date début</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $mutation)
                            <tr>
                                <td class="text-center">
                                    <span class="fw-semibold text-muted">#{{ $mutation['id'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $mutation['to_entite_name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div>
                                        <div class="fw-semibold">{{ $mutation['date_depot_formatted'] ?? '-' }}</div>
                                        <small class="text-muted">{{ $mutation['date_depot_time_formatted'] ?? '' }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($mutation['mutation_type'] === 'interne')
                                        <span class="badge bg-info">
                                            <i class="fas fa-arrow-right-arrow-left me-1"></i>Interne
                                        </span>
                                    @elseif($mutation['mutation_type'] === 'externe')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-external-link-alt me-1"></i>Externe
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $mutation['badge_class'] ?? 'bg-secondary' }}">
                                        <i class="fas {{ $mutation['icon_class'] ?? 'fa-info-circle' }} me-1"></i>{{ $mutation['statut'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($mutation['is_current_entity_validated'] ?? false)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Oui
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>Non
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(($mutation['is_destination_entity_validated'] ?? false) === null)
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-minus me-1"></i>N/A
                                        </span>
                                    @elseif($mutation['is_destination_entity_validated'] ?? false)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Oui
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i>Non
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($mutation['decision_conducteur_rh'])
                                        <span class="text-dark">{{ $mutation['decision_conducteur_rh'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($mutation['mutation_type'] === 'externe')
                                            {{-- For external mutations, show both direction validators --}}
                                            @if($mutation['valide_par_current'])
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                                        <i class="fas fa-user-check text-primary" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">Direction Actuelle:</small>
                                                        <span class="text-dark small">{{ $mutation['valide_par_current'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($mutation['valide_par_destination'])
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                                                        <i class="fas fa-user-check text-success" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">Direction Destination:</small>
                                                        <span class="text-dark small">{{ $mutation['valide_par_destination'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            {{-- For internal mutations, show current direction validator --}}
                                            @if($mutation['valide_par_current'])
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                                        <i class="fas fa-user-check text-primary" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">Direction Actuelle:</small>
                                                        <span class="text-dark small">{{ $mutation['valide_par_current'] }}</span>
                                                    </div>
                                                </div>
                                            @elseif($mutation['valide_par'])
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                                                        <i class="fas fa-user-check text-success" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="text-dark small">{{ $mutation['valide_par'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        @if(!$mutation['valide_par_current'] && !$mutation['valide_par_destination'] && !$mutation['valide_par'])
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($mutation['date_debut_affectation_formatted'])
                                        <div class="fw-semibold">{{ $mutation['date_debut_affectation_formatted'] }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($mutation['can_delete'] ?? false)
                                        <form action="{{ route('mutations.destroy', $mutation['id']) }}" method="POST" class="d-inline mutation-delete-form" data-skip-loading="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger mutation-delete-btn" 
                                                    title="Supprimer la demande"
                                                    data-mutation-id="{{ $mutation['id'] }}">
                                                <i class="fas fa-trash" id="deleteIcon-{{ $mutation['id'] }}"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (handled entirely on the frontend) -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    {{-- Content will be injected by JavaScript (see filterAndPaginateMutations / generatePaginationLinks) --}}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucune demande de mutation trouvée</h5>
                    <p class="text-muted small">Vous n'avez pas encore de demandes de mutation pour cette année</p>
                    @if(isset($hasPendingMutation) && $hasPendingMutation)
                        <button type="button" class="btn btn-primary mt-3" disabled title="Vous avez déjà une demande en attente. Vous ne pouvez pas créer une nouvelle demande tant que la précédente n'est pas traitée.">
                            <i class="fas fa-lock me-2"></i>Faire une Demande
                        </button>
                    @else
                        <a href="{{ route('mutations.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle me-2"></i>Faire une Demande
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Immediate cleanup function - runs before DOM is ready
(function() {
    function immediateCleanup() {
        // Remove any existing backdrops immediately
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.style.display = 'none';
            backdrop.remove();
        });
        
        // Remove modal-open class and reset styles
        if (document.body) {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            document.body.style.pointerEvents = '';
        }
        
        // Remove any hidden modals that might be blocking
        const allModals = document.querySelectorAll('.modal');
        allModals.forEach(function(modal) {
            if (!modal.classList.contains('show')) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                modal.removeAttribute('aria-modal');
            }
        });
    }
    
    // Run immediately
    immediateCleanup();
    
    // Also run when DOM is available
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', immediateCleanup);
    } else {
        immediateCleanup();
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    // Force cleanup on page load
    function forceCleanup() {
        // Remove all backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        
        // Remove modal-open class
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Hide any visible modals
        const visibleModals = document.querySelectorAll('.modal.show, .modal[style*="display: block"], .modal[style*="display:block"]');
        visibleModals.forEach(function(modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
        });
        
        // Remove any overlay elements
        const overlays = document.querySelectorAll('.modal-backdrop, [class*="overlay"], [class*="backdrop"]');
        overlays.forEach(function(overlay) {
            overlay.remove();
        });
    }
    
    // Run cleanup immediately
    forceCleanup();
    
    // Also run after a short delay to catch any late-loading elements
    setTimeout(forceCleanup, 100);
    setTimeout(forceCleanup, 500);
    
    // Frontend filtering - store all mutations data
    const allMutationsData = @json($allMutationsData ?? []);
    const filterForm = document.getElementById('filterForm');
    const yearSelect = document.getElementById('year');
    const perPageSelect = document.getElementById('per_page');
    const searchInput = document.getElementById('search');
    const tableBody = document.querySelector('table tbody');
    const paginationContainer = document.querySelector('.d-flex.justify-content-between.align-items-center.p-3.border-top');
    let currentPage = 1;
    // Global per-page value so other functions (like renderTable/renderPagination) can use it
    let perPage = perPageSelect ? parseInt(perPageSelect.value) || 25 : 25;

    // Filter and paginate mutations on frontend
    function filterAndPaginateMutations() {
        const selectedYear = yearSelect ? parseInt(yearSelect.value) : new Date().getFullYear();
        // Update global perPage from select if present
        perPage = perPageSelect ? (parseInt(perPageSelect.value) || perPage) : perPage;
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        // Filter mutations
        let filteredMutations = allMutationsData.filter(function(mutation) {
            // Filter by year
            const mutationYear = new Date(mutation.date_depot).getFullYear();
            if (mutationYear !== selectedYear) {
                return false;
            }

            // Filter by search term
            if (searchTerm) {
                const entityName = (mutation.to_entite_name || '').toLowerCase();
                if (!entityName.includes(searchTerm)) {
                    return false;
                }
            }

            return true;
        });

        // Calculate pagination
        const total = filteredMutations.length;
        const totalPages = Math.ceil(total / perPage);
        const startIndex = (currentPage - 1) * perPage;
        const endIndex = startIndex + perPage;
        const paginatedMutations = filteredMutations.slice(startIndex, endIndex);

        // Render table
        renderTable(paginatedMutations, total, startIndex, endIndex, currentPage, totalPages);

        // Update statistics (optional - can be calculated from filtered data)
        updateStatistics(filteredMutations);
    }

    // Render table rows
    function renderTable(mutations, total, startIndex, endIndex, currentPage, totalPages) {
        if (!tableBody) return;

        if (mutations.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-inbox fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Aucune demande de mutation trouvée</h5>
                        <p class="text-muted small">Aucune demande ne correspond aux critères de recherche</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        mutations.forEach(function(mutation) {
            const dateDepot = new Date(mutation.date_depot);
            const dateFormatted = dateDepot.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            const timeFormatted = dateDepot.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            
            const statutLower = mutation.statut.toLowerCase();
            let statutBadge = '';
            if (statutLower == 'rejeté' || statutLower == 'rejected') {
                statutBadge = `<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>${mutation.statut}</span>`;
            } else if (statutLower == 'validé' || statutLower == 'approved') {
                statutBadge = `<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>${mutation.statut}</span>`;
            } else if (statutLower == 'en attente validation rh') {
                statutBadge = `<span class="badge bg-warning text-dark"><i class="fas fa-user-tie me-1"></i>${mutation.statut}</span>`;
            } else if (statutLower == 'en attente' || statutLower == 'pending') {
                statutBadge = `<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>${mutation.statut}</span>`;
            } else if (statutLower == 'en cours de traitement') {
                statutBadge = `<span class="badge bg-info"><i class="fas fa-spinner me-1"></i>${mutation.statut}</span>`;
            } else {
                statutBadge = `<span class="badge bg-secondary">${mutation.statut}</span>`;
            }

            const mutationTypeBadge = mutation.mutation_type === 'interne' 
                ? `<span class="badge bg-info"><i class="fas fa-arrow-right-arrow-left me-1"></i>Interne</span>`
                : mutation.mutation_type === 'externe'
                ? `<span class="badge bg-warning text-dark"><i class="fas fa-external-link-alt me-1"></i>Externe</span>`
                : `<span class="badge bg-secondary">-</span>`;

            const isCurrentEntityValidated = mutation.approved_by_current_direction || mutation.is_validated_ent;
            const currentEntityBadge = isCurrentEntityValidated
                ? `<span class="badge bg-success"><i class="fas fa-check me-1"></i>Oui</span>`
                : `<span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Non</span>`;

            const isDestinationEntityValidated = mutation.mutation_type === 'externe' 
                ? (mutation.approved_by_destination_direction || mutation.valide_reception)
                : null;
            let destinationEntityBadge = '';
            if (isDestinationEntityValidated === null) {
                destinationEntityBadge = `<span class="badge bg-secondary"><i class="fas fa-minus me-1"></i>N/A</span>`;
            } else if (isDestinationEntityValidated) {
                destinationEntityBadge = `<span class="badge bg-success"><i class="fas fa-check me-1"></i>Oui</span>`;
            } else {
                destinationEntityBadge = `<span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Non</span>`;
            }

            const dateDebut = mutation.date_debut_affectation 
                ? new Date(mutation.date_debut_affectation).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
                : '-';

            const canDelete = (statutLower == 'en attente' || statutLower == 'pending') &&
                !mutation.approved_by_current_direction &&
                !mutation.approved_by_destination_direction &&
                !mutation.rejected_by_current_direction &&
                !mutation.rejected_by_destination_direction;

            const deleteButton = canDelete
                ? `<form action="/mutations/${mutation.id}" method="POST" class="d-inline mutation-delete-form" data-skip-loading="true">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger mutation-delete-btn" title="Supprimer la demande" data-mutation-id="${mutation.id}">
                        <i class="fas fa-trash" id="deleteIcon-${mutation.id}"></i>
                    </button>
                </form>`
                : '<span class="text-muted">-</span>';

            let validatedByHtml = '';
            if (mutation.mutation_type === 'externe') {
                if (mutation.valide_par_current) {
                    validatedByHtml += `<div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                            <i class="fas fa-user-check text-primary" style="font-size: 0.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Direction Actuelle:</small>
                            <span class="text-dark small">${mutation.valide_par_current}</span>
                        </div>
                    </div>`;
                }
                if (mutation.valide_par_destination) {
                    validatedByHtml += `<div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                            <i class="fas fa-user-check text-success" style="font-size: 0.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Direction Destination:</small>
                            <span class="text-dark small">${mutation.valide_par_destination}</span>
                        </div>
                    </div>`;
                }
            } else {
                if (mutation.valide_par_current) {
                    validatedByHtml += `<div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                            <i class="fas fa-user-check text-primary" style="font-size: 0.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Direction Actuelle:</small>
                            <span class="text-dark small">${mutation.valide_par_current}</span>
                        </div>
                    </div>`;
                } else if (mutation.valide_par) {
                    validatedByHtml += `<div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                            <i class="fas fa-user-check text-success" style="font-size: 0.75rem;"></i>
                        </div>
                        <div>
                            <span class="text-dark small">${mutation.valide_par}</span>
                        </div>
                    </div>`;
                }
            }
            if (!validatedByHtml) {
                validatedByHtml = '<span class="text-muted">-</span>';
            }

            html += `
                <tr>
                    <td class="text-center"><span class="fw-semibold text-muted">#${mutation.id}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                <i class="fas fa-building text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${mutation.to_entite_name}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div>
                            <div class="fw-semibold">${dateFormatted}</div>
                            <small class="text-muted">${timeFormatted}</small>
                        </div>
                    </td>
                    <td class="text-center">${mutationTypeBadge}</td>
                    <td class="text-center">${statutBadge}</td>
                    <td class="text-center">${currentEntityBadge}</td>
                    <td class="text-center">${destinationEntityBadge}</td>
                    <td>${mutation.decision_conducteur_rh || '<span class="text-muted">-</span>'}</td>
                    <td>
                        <div class="d-flex flex-column gap-1">${validatedByHtml}</div>
                    </td>
                    <td class="text-center">${dateDebut}</td>
                    <td class="text-center">${deleteButton}</td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Re-attach delete button handlers
        attachDeleteHandlers();

        // Render pagination
        renderPagination(total, startIndex, endIndex, currentPage, totalPages, perPage);
    }

    // Attach delete button handlers
    function attachDeleteHandlers() {
        document.querySelectorAll('.mutation-delete-form').forEach(function(form) {
            // Remove existing listeners to prevent duplicates
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            
            newForm.addEventListener('submit', function(e) {
                const button = this.querySelector('.mutation-delete-btn');
                const mutationId = button ? button.getAttribute('data-mutation-id') : null;
                const iconId = mutationId ? 'deleteIcon-' + mutationId : null;
                const icon = iconId ? document.getElementById(iconId) : button ? button.querySelector('i') : null;
                
                if (button && !button.hasAttribute('data-submitting')) {
                    button.setAttribute('data-submitting', 'true');
                    button.disabled = true;
                    
                    if (icon) {
                        button.setAttribute('data-original-icon', icon.className);
                        icon.className = 'fas fa-spinner fa-spin';
                    }
                }
            });
        });
    }

    // Render pagination
    function renderPagination(total, startIndex, endIndex, currentPage, totalPages, perPage) {
        if (!paginationContainer) return;

        const paginationHtml = `
            <div class="text-muted small">
                Affichage de ${startIndex + 1} à ${Math.min(endIndex, total)} sur ${total} demande(s)
            </div>
            <div>
                ${generatePaginationLinks(currentPage, totalPages)}
            </div>
        `;
        paginationContainer.innerHTML = paginationHtml;
    }

    // Generate pagination links
    function generatePaginationLinks(currentPage, totalPages) {
        if (totalPages <= 1) return '';

        let html = '<nav><ul class="pagination mb-0">';
        
        // Previous button
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" ${currentPage === 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>`;

        // Page numbers
        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Next button
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>`;

        html += '</ul></nav>';
        return html;
    }

    // Update statistics (optional)
    function updateStatistics(filteredMutations) {
        // Statistics are calculated from all data, not filtered
        // So we don't update them here, or we can if needed
    }

    // Event listeners for filters
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            currentPage = 1;
            filterAndPaginateMutations();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            filterAndPaginateMutations();
        });
    }

    // Debounce search input
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentPage = 1;
                filterAndPaginateMutations();
            }, 300);
        });
    }

    // Prevent form submission
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            filterAndPaginateMutations();
        });
    }

    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.page-link')) {
            e.preventDefault();
            const pageLink = e.target.closest('.page-link');
            const page = parseInt(pageLink.getAttribute('data-page'));
            if (page && page !== currentPage) {
                currentPage = page;
                filterAndPaginateMutations();
                // Scroll to top of table
                if (tableBody) {
                    tableBody.closest('.table-responsive')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    });

    // Initial render
    filterAndPaginateMutations();

    // Ensure modals properly close and remove backdrop
    const deleteModals = document.querySelectorAll('[id^="deleteModal"]');
    deleteModals.forEach(function(modalElement) {
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            console.warn('Bootstrap Modal not available');
            return;
        }
        
        let modal;
        try {
            modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        } catch (e) {
            console.error('Error creating modal instance:', e);
            return;
        }
        
        // Clean up backdrop when modal is hidden
        modalElement.addEventListener('hidden.bs.modal', function() {
            forceCleanup();
        });

        // Ensure backdrop is removed when modal starts hiding
        modalElement.addEventListener('hide.bs.modal', function() {
            // Don't prevent default, just prepare for cleanup
        });
        
        // Also listen for click on backdrop
        modalElement.addEventListener('click', function(e) {
            if (e.target === modalElement) {
                // Clicked on backdrop, close modal
                modal.hide();
                forceCleanup();
            }
        });
    });
    
    // Add keyboard shortcut to force cleanup (Ctrl+Shift+C or Escape)
    document.addEventListener('keydown', function(e) {
        // Escape key to close any open modals
        if (e.key === 'Escape') {
            const visibleModals = document.querySelectorAll('.modal.show');
            visibleModals.forEach(function(modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
            forceCleanup();
        }
        
        // Ctrl+Shift+C to force cleanup
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            forceCleanup();
            console.log('Forced cleanup executed');
            alert('Nettoyage forcé effectué. La page devrait maintenant être cliquable.');
        }
    });
    
    // Expose cleanup function to window for console access
    window.forceModalCleanup = forceCleanup;
    console.log('Type "forceModalCleanup()" in console to manually clean up stuck modals');
    
    // Periodically check for orphaned backdrops (every 2 seconds)
    setInterval(function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 0) {
            const visibleModals = document.querySelectorAll('.modal.show');
            if (visibleModals.length === 0) {
                // There are backdrops but no visible modals - clean up
                forceCleanup();
            }
        }
    }, 2000);
    
    // Initial attachment of delete handlers (for server-rendered content)
    attachDeleteHandlers();
});
</script>
@endpush
@endsection

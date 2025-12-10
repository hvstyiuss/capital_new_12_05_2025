@extends('layouts.app')

@section('title', 'Demande de mutation')

@push('styles')
<style>
    /* Disable all fade/transition animations on Flowbite modals */
    [id^="approveModal"],
    [id^="rejectModal"] {
        transition: none !important;
        animation: none !important;
    }
    
    [id^="approveModal"] *,
    [id^="rejectModal"] * {
        transition: none !important;
        animation: none !important;
    }
    
    /* Ensure modal backdrop doesn't have fade */
    [id^="approveModal"]::before,
    [id^="rejectModal"]::before,
    [id^="approveModal"]::after,
    [id^="rejectModal"]::after {
        display: none !important;
    }
    
    /* Force modal to be immediately visible when shown */
    [id^="approveModal"]:not(.hidden),
    [id^="rejectModal"]:not(.hidden) {
        display: flex !important;
        opacity: 1 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Demande de mutation</h1>
            <p class="text-muted mb-0">Approuver ou rejeter la réception des demandes de mutation à la destination</p>
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
                                <i class="fas fa-exchange-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Demandes</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalMutations ?? $mutations->total()) }}</h4>
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
                            <p class="text-muted mb-0 small">Approuvées</p>
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
            <form method="GET" action="{{ route('mutations.super-rh.destination-requests') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label small text-muted">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="" {{ $status === '' || $status === null ? 'selected' : '' }}>Tous</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvées</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejetées</option>
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
                                   placeholder="Rechercher par nom, PPR ou entité..." 
                                   value="{{ $search }}"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table -->
    <x-mutations.table 
        :mutations="$mutations" 
        :pagination="$mutations->appends(request()->query())->links()"
        :showPagination="true"
    >
        <x-slot:headers>
            <th class="border-0 text-center" style="width: 60px;">Nº</th>
            <th class="border-0">Agent</th>
            <th class="border-0">Entité Actuelle</th>
            <th class="border-0">Entité de Destination</th>
            <th class="border-0 text-center">Date de Dépôt</th>
            <th class="border-0 text-center">Type</th>
            <th class="border-0 text-center">Validé Par</th>
            <th class="border-0 text-center">Statut</th>
            <th class="border-0 text-center">Actions</th>
        </x-slot:headers>
        
        @foreach($mutations as $mutation)
        <tr>
            <td class="text-center">
                <span class="fw-semibold text-muted">#{{ $mutation['id'] }}</span>
            </td>
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
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-2">
                        <i class="fas fa-building text-info"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $mutation['current_entite_name'] }}</div>
                        <small class="text-muted">Actuelle</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                        <i class="fas fa-building text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $mutation['to_entite_name'] }}</div>
                        @if($mutation['motif'])
                        <small class="text-muted">{{ $mutation['motif'] }}</small>
                        @else
                        <small class="text-muted">Destination</small>
                        @endif
                    </div>
                </div>
            </td>
            <td class="text-center">
                <div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($mutation['date_depot'])->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($mutation['date_depot'])->format('H:i') }}</small>
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
                <div class="d-flex flex-column gap-2 align-items-center">
                    @if($mutation['valide_par_current'])
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-primary" style="font-size: 10px;"></i>
                            </div>
                            <div class="text-start">
                                <div class="small fw-semibold">Direction Actuelle</div>
                                <div class="small text-muted">{{ $mutation['valide_par_current']['name'] }}</div>
                            </div>
                        </div>
                    @endif
                    @if($mutation['valide_par_destination'])
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-success bg-opacity-10 rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-success" style="font-size: 10px;"></i>
                            </div>
                            <div class="text-start">
                                <div class="small fw-semibold">Direction Destination</div>
                                <div class="small text-muted">{{ $mutation['valide_par_destination']['name'] }}</div>
                            </div>
                        </div>
                    @endif
                    @if(!$mutation['valide_par_current'] && !$mutation['valide_par_destination'])
                        <span class="text-muted small">-</span>
                    @endif
                </div>
            </td>
            <td class="text-center">
                @php
                    $statutLower = strtolower($mutation['statut']);
                @endphp
                @if($statutLower == 'réception rejetée' || $statutLower == 'rejected')
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>{{ $mutation['statut'] }}
                    </span>
                @elseif($statutLower == 'réception approuvée' || $statutLower == 'approved')
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>{{ $mutation['statut'] }}
                    </span>
                @elseif($statutLower == 'envoyée à destination')
                    <span class="badge bg-info">
                        <i class="fas fa-paper-plane me-1"></i>{{ $mutation['statut'] }}
                    </span>
                @elseif($statutLower == 'en attente validation réception' || $statutLower == 'en attente' || $statutLower == 'pending')
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-clock me-1"></i>{{ $mutation['statut'] }}
                    </span>
                @else
                    <span class="badge bg-secondary">{{ $mutation['statut'] }}</span>
                @endif
            </td>
            <td class="text-center">
                @php
                    $isPending = in_array($mutation['statut'], ['En attente validation réception', 'En attente', 'En attente validation finale']);
                    $isFinalValidation = isset($mutation['is_ready_for_final_validation']) && $mutation['is_ready_for_final_validation'];
                    $isApproved = strtolower($mutation['statut']) === 'réception approuvée' || strtolower($mutation['statut']) === 'approved';
                    $isRejected = strtolower($mutation['statut']) === 'réception rejetée' || strtolower($mutation['statut']) === 'rejected';
                @endphp
                @if($isPending)
                    <div class="btn-group" role="group">
                        <button type="button" 
                                class="btn btn-sm btn-success" 
                                onclick="handleApprove({{ $mutation['id'] }}, '{{ $mutation['mutation_type'] }}', {{ $isFinalValidation ? 'true' : 'false' }}, '{{ route('mutations.super-rh.destination-approve', $mutation['id']) }}')"
                                title="{{ $isFinalValidation ? 'Valider avec date de début' : 'Approuver la réception' }}">
                            <i class="fas fa-check me-1"></i>{{ $isFinalValidation ? 'Valider' : 'Approuver' }}
                        </button>
                        <button type="button" 
                                class="btn btn-sm btn-danger" 
                                onclick="handleReject({{ $mutation['id'] }}, '{{ route('mutations.super-rh.destination-reject', $mutation['id']) }}')"
                                title="Rejeter la réception">
                            <i class="fas fa-times me-1"></i>Rejeter
                        </button>
                    </div>
                @elseif($isApproved)
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Validée
                    </span>
                @elseif($isRejected)
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>Rejetée
                    </span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
        </tr>
        @endforeach
    </x-mutations.table>
</div>

<!-- No modals - using simple JavaScript alerts -->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const statusSelect = document.getElementById('status');
    const perPageSelect = document.getElementById('per_page');
    const searchInput = document.getElementById('search');

    // Auto-submit on filter change
    if (statusSelect && perPageSelect) {
        [statusSelect, perPageSelect].forEach(function(element) {
            element.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }

    // Debounce search input
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                filterForm.submit();
            }, 500);
        });
    }
});

// Simple JavaScript functions for approve/reject - no modals, just alerts
function handleApprove(mutationId, mutationType, isFinalValidation, approveRoute) {
    let message = '';
    let needsDate = false;
    
    if (isFinalValidation) {
        message = 'Êtes-vous sûr de vouloir valider cette mutation ?\n\nVeuillez définir la date de début d\'affectation.';
        needsDate = true;
    } else if (mutationType === 'externe') {
        message = 'Êtes-vous sûr de vouloir approuver cette mutation ?\n\nElle sera envoyée à la direction de destination pour validation.';
        needsDate = false;
    } else {
        message = 'Êtes-vous sûr de vouloir approuver cette mutation ?\n\nVeuillez définir la date de début d\'affectation.';
        needsDate = true;
    }
    
    if (confirm(message)) {
        if (needsDate) {
            // Prompt for date
            const today = new Date().toISOString().split('T')[0];
            const dateInput = prompt('Veuillez entrer la date de début d\'affectation (format: YYYY-MM-DD):\n\nLa date doit être aujourd\'hui ou une date future.', today);
            
            if (!dateInput) {
                alert('La date est requise. Opération annulée.');
                return;
            }
            
            // Validate date
            const selectedDate = new Date(dateInput);
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);
            
            if (selectedDate < todayDate) {
                alert('La date doit être aujourd\'hui ou une date future.');
                return;
            }
            
            // Submit form with date
            submitApproveForm(approveRoute, dateInput);
        } else {
            // Submit form without date
            submitApproveForm(approveRoute, null);
        }
    }
}

function handleReject(mutationId, rejectRoute) {
    const message = 'Êtes-vous sûr de vouloir rejeter la réception de cette mutation ?';
    
    if (confirm(message)) {
        const reason = prompt('Veuillez expliquer la raison du rejet (minimum 10 caractères):');
        
        if (!reason) {
            alert('La raison du rejet est requise. Opération annulée.');
            return;
        }
        
        if (reason.trim().length < 10) {
            alert('La raison du rejet doit contenir au moins 10 caractères.');
            return;
        }
        
        // Submit form with reason
        submitRejectForm(rejectRoute, reason);
    }
}

function submitApproveForm(route, dateDebut) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    // Add date if provided
    if (dateDebut) {
        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'date_debut_affectation';
        dateInput.value = dateDebut;
        form.appendChild(dateInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

function submitRejectForm(route, reason) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    // Add rejection reason
    const reasonInput = document.createElement('input');
    reasonInput.type = 'hidden';
    reasonInput.name = 'rejection_reason_super_rh';
    reasonInput.value = reason;
    form.appendChild(reasonInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection

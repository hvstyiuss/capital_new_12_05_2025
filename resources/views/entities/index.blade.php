@extends('layouts.app')

@section('title', 'Entités')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-building me-2"></i>
                    Entités
                </h1>
                <p class="text-muted mb-0">Gestion des entités organisationnelles</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreurs:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold mb-2">
                        <i class="fas fa-search me-2 text-muted"></i>Rechercher
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nom de l'entité ou description...">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label fw-semibold mb-2">
                        <i class="fas fa-filter me-2 text-muted"></i>Type
                    </label>
                    <select class="form-select form-select-lg" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="central" {{ request('type') == 'central' ? 'selected' : '' }}>Central</option>
                        <option value="regional" {{ request('type') == 'regional' ? 'selected' : '' }}>Régional</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input form-check-input-lg" 
                               type="checkbox" 
                               id="vacant" 
                               name="vacant" 
                               value="1"
                               {{ request('vacant') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="vacant">
                            <i class="fas fa-user-slash me-2 text-muted"></i>Postes vacants
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary w-100" style="display: none;">
                        <i class="fas fa-redo me-2"></i>
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Entities Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list me-2"></i>Liste des entités
                </h5>
                <span class="badge bg-primary rounded-pill">{{ $entites->total() }} entité(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle no-row-click" id="entitiesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Nom</th>
                            <th class="fw-semibold">Type</th>
                            <th class="fw-semibold">Description</th>
                            <th class="fw-semibold">Parent</th>
                            <th class="fw-semibold">Date Début</th>
                            <th class="fw-semibold">Date Fin</th>
                            <th class="fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="entitiesTableBody">
                        @forelse($entites as $entite)
                            <tr data-entity-id="{{ $entite->id }}" 
                                data-entity-name="{{ strtolower($entite->name) }}"
                                data-entity-type="{{ $entite->entiteInfo ? $entite->entiteInfo->type : '' }}"
                                data-entity-description="{{ $entite->entiteInfo && $entite->entiteInfo->description ? strtolower($entite->entiteInfo->description) : '' }}">
                                <td class="fw-medium">{{ $entite->id }}</td>
                                <td>
                                    <strong class="text-dark">{{ $entite->name }}</strong>
                                </td>
                                <td>
                                    @if($entite->entiteInfo && $entite->entiteInfo->type)
                                        @if($entite->entiteInfo->type == 'central')
                                            <span class="badge bg-primary rounded-pill">Central</span>
                                        @elseif($entite->entiteInfo->type == 'regional')
                                            <span class="badge bg-info rounded-pill">Régional</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">{{ $entite->entiteInfo->type }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Non défini</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $entite->entiteInfo && $entite->entiteInfo->description ? Str::limit($entite->entiteInfo->description, 50) : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="parent-select-wrapper">
                                        <select class="form-select form-select-sm parent-select" 
                                                data-entite-id="{{ $entite->id }}"
                                                id="parent-select-{{ $entite->id }}"
                                                style="min-width: 200px; width: 100%;">
                                            <option value="">Aucune (entité racine)</option>
                                            @foreach($allEntities as $parentEntity)
                                                @if($parentEntity->id != $entite->id)
                                                    <option value="{{ $parentEntity->id }}" 
                                                            {{ $entite->parent_id == $parentEntity->id ? 'selected' : '' }}>
                                                        {{ $parentEntity->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $entite->date_debut ? $entite->date_debut->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $entite->date_fin ? $entite->date_fin->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('entities.users', $entite) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Voir les personnes"
                                       onclick="event.stopPropagation();">
                                        <i class="fas fa-users me-1"></i>
                                        Personnes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucune entité trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($entites->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Affichage de <strong>{{ $entites->firstItem() }}</strong> à <strong>{{ $entites->lastItem() }}</strong> 
                        sur <strong>{{ $entites->total() }}</strong> entité(s)
                    </div>
                    <div>
                        {{ $entites->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


@push('styles')
<style>
    .parent-select-wrapper {
        position: relative;
        min-width: 200px;
    }
    
    .parent-select {
        font-size: 0.875rem;
        padding: 0.25rem 2rem 0.25rem 0.5rem;
        width: 100%;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        transition: all 0.15s ease-in-out;
    }
    
    .parent-select:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .parent-select.loading {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%23000' d='M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z' opacity='.25'/%3E%3Cpath fill='%23000' d='M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z'%3E%3CanimateTransform attributeName='transform' dur='0.75s' repeatCount='indefinite' type='rotate' values='0 12 12;360 12 12'/%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 16px 16px;
        padding-right: 2.5rem;
    }
    
    .parent-select.success {
        border-color: #198754;
        background-color: #f0f9f4;
    }
    
    .parent-select.error {
        border-color: #dc3545;
        background-color: #fff5f5;
    }


    .table th {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }

    .card {
        border-radius: 0.5rem;
    }

    .badge {
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        'use strict';
        
        // Frontend filtering - store all entities data
        const allEntitesData = @json($allEntitesData ?? []);
        const allEntities = @json($allEntities ?? []);
        const searchInput = document.getElementById('search');
        const typeSelect = document.getElementById('type');
        const vacantCheckbox = document.getElementById('vacant');
        const tableBody = document.getElementById('entitiesTableBody');
        const paginationContainer = document.querySelector('.card-footer');
        let currentPage = 1;
        const perPage = 20;
        let searchTimeout;
        
        // Filter and paginate entities on frontend
        function filterAndPaginateEntities() {
            const searchTerm = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const selectedType = typeSelect ? typeSelect.value : '';
            const showVacant = vacantCheckbox ? vacantCheckbox.checked : false;
            
            // Filter entities
            let filteredEntities = allEntitesData.filter(function(entite) {
                // Filter by type
                if (selectedType && entite.type !== selectedType) {
                    return false;
                }
                
                // Filter by vacant (chef_ppr is null)
                if (showVacant && entite.chef_ppr !== null) {
                    return false;
                }
                
                // Filter by search term (name or description)
                if (searchTerm) {
                    const name = (entite.name || '').toLowerCase();
                    const description = (entite.description || '').toLowerCase();
                    
                    if (!name.includes(searchTerm) && !description.includes(searchTerm)) {
                        return false;
                    }
                }
                
                return true;
            });
            
            // Calculate pagination
            const total = filteredEntities.length;
            const totalPages = Math.ceil(total / perPage);
            const startIndex = (currentPage - 1) * perPage;
            const endIndex = startIndex + perPage;
            const paginatedEntities = filteredEntities.slice(startIndex, endIndex);
            
            // Render table
            renderEntities(paginatedEntities, total, startIndex, endIndex, currentPage, totalPages);
        }
        
        // Render entities table
        function renderEntities(entities, total, startIndex, endIndex, currentPage, totalPages) {
            if (!tableBody) return;
            
            if (entities.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune entité trouvée</p>
                        </td>
                    </tr>
                `;
                renderPagination(total, startIndex, endIndex, currentPage, totalPages);
                return;
            }
            
            let html = '';
            entities.forEach(function(entite) {
                const dateDebut = entite.date_debut ? new Date(entite.date_debut).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '-';
                const dateFin = entite.date_fin ? new Date(entite.date_fin).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '-';
                
                // Type badge
                let typeBadge = '';
                if (entite.type === 'central') {
                    typeBadge = '<span class="badge bg-primary rounded-pill">Central</span>';
                } else if (entite.type === 'regional') {
                    typeBadge = '<span class="badge bg-info rounded-pill">Régional</span>';
                } else if (entite.type) {
                    typeBadge = `<span class="badge bg-secondary rounded-pill">${entite.type}</span>`;
                } else {
                    typeBadge = '<span class="badge bg-secondary rounded-pill">Non défini</span>';
                }
                
                // Description (truncated)
                const description = entite.description ? (entite.description.length > 50 ? entite.description.substring(0, 50) + '...' : entite.description) : '-';
                
                // Parent select options
                let parentSelectOptions = '<option value="">Aucune (entité racine)</option>';
                allEntities.forEach(function(parentEntity) {
                    if (parentEntity.id != entite.id) {
                        const selected = entite.parent_id == parentEntity.id ? 'selected' : '';
                        parentSelectOptions += `<option value="${parentEntity.id}" ${selected}>${parentEntity.name}</option>`;
                    }
                });
                
                html += `
                    <tr data-entity-id="${entite.id}" 
                        data-entity-name="${(entite.name || '').toLowerCase()}"
                        data-entity-type="${entite.type || ''}"
                        data-entity-description="${(entite.description || '').toLowerCase()}">
                        <td class="fw-medium">${entite.id}</td>
                        <td>
                            <strong class="text-dark">${entite.name}</strong>
                        </td>
                        <td>${typeBadge}</td>
                        <td>
                            <span class="text-muted">${description}</span>
                        </td>
                        <td>
                            <div class="parent-select-wrapper">
                                <select class="form-select form-select-sm parent-select" 
                                        data-entite-id="${entite.id}"
                                        id="parent-select-${entite.id}"
                                        style="min-width: 200px; width: 100%;">
                                    ${parentSelectOptions}
                                </select>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">${dateDebut}</span>
                        </td>
                        <td>
                            <span class="text-muted">${dateFin}</span>
                        </td>
                        <td class="text-center">
                            <a href="/entities/${entite.id}/users" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Voir les personnes"
                               onclick="event.stopPropagation();">
                                <i class="fas fa-users me-1"></i>
                                Personnes
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
            
            // Re-initialize parent selects
            initParentSelects();
            
            // Update total count badge
            const totalBadge = document.querySelector('.card-header .badge');
            if (totalBadge) {
                totalBadge.textContent = `${total} entité(s)`;
            }
            
            // Render pagination
            renderPagination(total, startIndex, endIndex, currentPage, totalPages);
        }
        
        // Render pagination
        function renderPagination(total, startIndex, endIndex, currentPage, totalPages) {
            if (!paginationContainer) return;
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Affichage de <strong>${startIndex + 1}</strong> à <strong>${Math.min(endIndex, total)}</strong> 
                            sur <strong>${total}</strong> entité(s)
                        </div>
                        <div></div>
                    </div>
                `;
                return;
            }
            
            const paginationHtml = `
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Affichage de <strong>${startIndex + 1}</strong> à <strong>${Math.min(endIndex, total)}</strong> 
                        sur <strong>${total}</strong> entité(s)
                    </div>
                    <div>
                        ${generatePaginationLinks(currentPage, totalPages)}
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = paginationHtml;
        }
        
        // Generate pagination links
        function generatePaginationLinks(currentPage, totalPages) {
            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }
            
            let html = '<nav><ul class="pagination mb-0">';
            
            // Previous button
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" ${currentPage === 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;
            
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
        
        // Update reset button visibility
        function updateResetButton() {
            const resetBtn = document.getElementById('resetFiltersBtn');
            if (resetBtn) {
                const hasSearch = searchInput && searchInput.value.trim();
                const hasType = typeSelect && typeSelect.value;
                const hasVacant = vacantCheckbox && vacantCheckbox.checked;
                resetBtn.style.display = (hasSearch || hasType || hasVacant) ? 'block' : 'none';
            }
        }
        
        // Reset filters
        function resetFilters() {
            if (searchInput) searchInput.value = '';
            if (typeSelect) typeSelect.value = '';
            if (vacantCheckbox) vacantCheckbox.checked = false;
            currentPage = 1;
            updateResetButton();
            filterAndPaginateEntities();
        }
        
        // Event listeners for filters
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentPage = 1;
                    updateResetButton();
                    filterAndPaginateEntities();
                }, 300);
            });
        }
        
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                currentPage = 1;
                updateResetButton();
                filterAndPaginateEntities();
            });
        }
        
        if (vacantCheckbox) {
            vacantCheckbox.addEventListener('change', function() {
                currentPage = 1;
                updateResetButton();
                filterAndPaginateEntities();
            });
        }
        
        // Reset button click
        const resetBtn = document.getElementById('resetFiltersBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetFilters);
        }
        
        // Initial reset button state
        updateResetButton();
        
        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const pageLink = e.target.closest('.page-link');
                const page = parseInt(pageLink.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    filterAndPaginateEntities();
                    // Scroll to top of table
                    if (tableBody) {
                        tableBody.closest('.table-responsive')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }
        });
        
        // Initial render
        filterAndPaginateEntities();
        
        // Initialize parent selects with search functionality
        function initParentSelects() {
            document.querySelectorAll('.parent-select').forEach(select => {
                let previousValue = select.value;
                let isUpdating = false;
                
                // Store original options for filtering
                const originalOptions = Array.from(select.options).map(opt => ({
                    value: opt.value,
                    text: opt.text,
                    element: opt
                }));
                
                // Handle change event to update parent via AJAX
                select.addEventListener('change', function() {
                    if (isUpdating) return;
                    
                    const entiteId = this.dataset.entiteId;
                    const parentId = this.value;
                    const selectElement = this;
                    
                    // Store previous value
                    previousValue = selectElement.value;
                    
                    // Disable select during update
                    selectElement.disabled = true;
                    selectElement.classList.add('loading');
                    selectElement.classList.remove('success', 'error');
                    
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    
                    // Send AJAX request
                    fetch(`/entities/${entiteId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            parent_id: parentId || null
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Success
                        selectElement.classList.remove('loading');
                        selectElement.classList.add('success');
                        selectElement.disabled = false;
                        
                        // Show success message
                        if (window.UXUtils && window.UXUtils.showToast) {
                            window.UXUtils.showToast('Parent mis à jour avec succès', 'success');
                        } else if (window.showToast) {
                            window.showToast('Parent mis à jour avec succès', 'success');
                        }
                        
                        // Remove success class after 2 seconds
                        setTimeout(() => {
                            selectElement.classList.remove('success');
                        }, 2000);
                    })
                    .catch(error => {
                        // Error - revert to previous value
                        selectElement.classList.remove('loading');
                        selectElement.classList.add('error');
                        selectElement.value = previousValue;
                        selectElement.disabled = false;
                        
                        // Show error message
                        const errorMsg = error.message || (error.errors && error.errors.parent_id && error.errors.parent_id[0]) || 'Erreur lors de la mise à jour';
                        if (window.UXUtils && window.UXUtils.showToast) {
                            window.UXUtils.showToast(errorMsg, 'error');
                        } else if (window.showToast) {
                            window.showToast(errorMsg, 'error');
                        } else {
                            alert('Erreur: ' + errorMsg);
                        }
                        
                        // Remove error class after 2 seconds
                        setTimeout(() => {
                            selectElement.classList.remove('error');
                        }, 2000);
                    });
                });
            });
        }
        
        // Initialize parent selects
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initParentSelects);
        } else {
            initParentSelects();
        }
        
        // Prevent row clicks from triggering navigation
        // Ensure only the button is clickable, not the entire row
        function preventRowClicks() {
            document.querySelectorAll('#entitiesTable tbody tr').forEach(row => {
                row.addEventListener('click', function(e) {
                    // Only allow clicks on buttons, links, or selects
                    if (!e.target.closest('a, button, select, .parent-select')) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                });
                
                // Make sure buttons and links stop propagation
                row.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            });
        }
        
        // Initialize row click prevention
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', preventRowClicks);
        } else {
            preventRowClicks();
        }
    })();
</script>
@endpush
@endsection





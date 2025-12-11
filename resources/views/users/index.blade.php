@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Gestion des Utilisateurs
                </h1>
                <p class="text-muted mb-0">Administration des utilisateurs et des rôles</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('hr.users.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nouvel Utilisateur
                </a>
                <a href="{{ route('hr.users.swap-chefs') }}" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Échange de Chefs
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Utilisateurs</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalUsers ?? $users->total()) }}</h3>
                            <small class="text-muted">Enregistrés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(25, 135, 84, 0.1);">
                                <i class="fas fa-user-check fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Utilisateurs Actifs</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($activeUsers ?? 0) }}</h3>
                            <small class="text-muted">Actuellement actifs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="fas fa-shield-alt fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Rôles Créés</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $roles->count() }}</h3>
                            <small class="text-muted">Rôles disponibles</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                <i class="fas fa-calendar-plus fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Nouveaux (30j)</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($newUsers30d ?? 0) }}</h3>
                            <small class="text-muted">Derniers 30 jours</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-filter me-2 text-primary"></i>Filtres
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('hr.users.index') }}" id="filterForm" class="row g-3">
                <div class="col-md-2 col-lg-2">
                    <label for="role" class="form-label fw-semibold small">
                        <i class="fas fa-shield-alt me-1 text-muted"></i>Rôle
                    </label>
                    <select id="role" name="role" class="form-select form-select-sm">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label for="status" class="form-label fw-semibold small">
                        <i class="fas fa-toggle-on me-1 text-muted"></i>Statut
                    </label>
                    <select id="status" name="status" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label for="search" class="form-label fw-semibold small">
                        <i class="fas fa-search me-1 text-muted"></i>Rechercher
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="PPR, nom, email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3 col-lg-3">
                    <label for="entite_id" class="form-label fw-semibold small">
                        <i class="fas fa-building me-1 text-muted"></i>Entité
                    </label>
                    <select id="entite_id" name="entite_id" class="form-select form-select-sm">
                        <option value="">Toutes les entités</option>
                        @isset($entites)
                            @foreach($entites as $entite)
                                <option value="{{ $entite->id }}" {{ (string)request('entite_id') === (string)$entite->id ? 'selected' : '' }}>
                                    {{ $entite->name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label for="per_page" class="form-label fw-semibold small">
                        <i class="fas fa-list me-1 text-muted"></i>Afficher
                    </label>
                    <select id="per_page" name="per_page" class="form-select form-select-sm">
                        <option value="10" {{ (int)request('per_page', 20) === 10 ? 'selected' : '' }}>10 lignes</option>
                        <option value="20" {{ (int)request('per_page', 20) === 20 ? 'selected' : '' }}>20 lignes</option>
                        <option value="50" {{ (int)request('per_page', 20) === 50 ? 'selected' : '' }}>50 lignes</option>
                        <option value="100" {{ (int)request('per_page', 20) === 100 ? 'selected' : '' }}>100 lignes</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-table me-2 text-primary"></i>Liste des Utilisateurs
                </h5>
                @if(request('role') || request('status') || request('search') || request('entite_id'))
                    <a href="{{ route('hr.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Réinitialiser
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0" id="tableContainer">
            @include('users.partials.table', ['users' => $users])
            
            <!-- Pagination -->
            @if($users->hasPages())
            <div class="card-footer bg-white border-top py-3" id="paginationContainer">
                @include('users.partials.pagination', ['users' => $users])
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function handleToggleStatus(userPpr, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activer' : 'désactiver';
    const message = `Êtes-vous sûr de vouloir ${action} cet utilisateur ?`;
    
    if (!confirm(message)) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('Erreur: Token CSRF manquant. Veuillez rafraîchir la page.');
        return;
    }
    
    fetch(`/hr/users/${userPpr}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ is_active: newStatus === 'active' })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reload the table via AJAX instead of full page reload
            const currentUrl = new URL(window.location.href);
            loadUsers(currentUrl.toString());
        } else {
            alert('Erreur lors du changement de statut: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du changement de statut. Veuillez réessayer.\n\nDétails: ' + error.message);
    });
}

function toggleUserStatus(userPpr, newStatus) {
    const action = newStatus === 'active' ? 'activer' : 'désactiver';
    const message = `Êtes-vous sûr de vouloir ${action} cet utilisateur ?`;
    
    if (!confirm(message)) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('Erreur: Token CSRF manquant. Veuillez rafraîchir la page.');
        return;
    }
    
    fetch(`/hr/users/${userPpr}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ is_active: newStatus === 'active' })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reload the table via AJAX instead of full page reload
            const currentUrl = new URL(window.location.href);
            loadUsers(currentUrl.toString());
        } else {
            alert('Erreur lors du changement de statut: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du changement de statut. Veuillez réessayer.\n\nDétails: ' + error.message);
    });
}

    // Load users via AJAX
    function loadUsers(url) {
        const tableContainer = document.getElementById('tableContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        
        // Show loading state
        if (tableContainer) {
            tableContainer.style.opacity = '0.6';
            tableContainer.style.pointerEvents = 'none';
        }
        
        // Update URL without page reload
        if (url) {
            const urlObj = new URL(url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');
            url = urlObj.toString();
        } else {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('ajax', '1');
            url = currentUrl.toString();
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html && tableContainer) {
                tableContainer.innerHTML = data.html;
                // Update pagination if exists
                if (data.pagination) {
                    const paginationContainer = document.getElementById('paginationContainer');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    } else if (data.pagination.trim() !== '') {
                        // Create pagination container if it doesn't exist
                        const footer = document.createElement('div');
                        footer.className = 'card-footer bg-white border-top py-3';
                        footer.id = 'paginationContainer';
                        footer.innerHTML = data.pagination;
                        tableContainer.appendChild(footer);
                    }
                } else {
                    // Remove pagination if no pages
                    const paginationContainer = document.getElementById('paginationContainer');
                    if (paginationContainer) {
                        paginationContainer.remove();
                    }
                }
                // Re-initialize event listeners
                initializeEventListeners();
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            // Fallback to page reload
            window.location.href = url.replace('&ajax=1', '').replace('?ajax=1', '');
        })
        .finally(() => {
            if (tableContainer) {
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            }
        });
    }
    
    // Initialize event listeners
    function initializeEventListeners() {
        // Pagination links
        document.querySelectorAll('.pagination-modern .page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url) {
                    loadUsers(url);
                    // Update URL in browser
                    window.history.pushState({}, '', url);
                }
            });
        });
    }
    
    // Auto-submit filter form on change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const roleSelect = document.getElementById('role');
    const statusSelect = document.getElementById('status');
    const searchInput = document.getElementById('search');
    const entiteSelect = document.getElementById('entite_id');
    const perPageSelect = document.getElementById('per_page');
    
    let searchTimeout;
    
    // Initialize pagination listeners
    initializeEventListeners();
    
    // Helper function to submit filter form via AJAX
    function submitFilterForm() {
        const formData = new FormData(filterForm);
        formData.append('ajax', '1');
        const url = new URL(filterForm.action);
        formData.forEach((value, key) => {
            url.searchParams.set(key, value);
        });
        loadUsers(url.toString());
        // Update URL in browser
        window.history.pushState({}, '', url.toString().replace('&ajax=1', '').replace('?ajax=1', ''));
    }
    
    // Auto-submit on role or status change
    if (roleSelect) {
        roleSelect.addEventListener('change', submitFilterForm);
    }
    
    if (statusSelect) {
        statusSelect.addEventListener('change', submitFilterForm);
    }

    if (entiteSelect) {
        entiteSelect.addEventListener('change', submitFilterForm);
    }
    
    if (perPageSelect) {
        perPageSelect.addEventListener('change', submitFilterForm);
    }
    
    // Debounced search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const formData = new FormData(filterForm);
                formData.append('ajax', '1');
                const url = new URL(filterForm.action);
                formData.forEach((value, key) => {
                    url.searchParams.set(key, value);
                });
                loadUsers(url.toString());
            }, 500);
        });
    }
    
    // Handle toggle status buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-status-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.toggle-status-btn');
            const userPpr = button.getAttribute('data-user-ppr');
            const currentStatus = button.getAttribute('data-user-status');
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            if (userPpr) {
                toggleUserStatus(userPpr, newStatus);
            }
        }
    });
    
    // Prevent row clicks from triggering navigation
    // Ensure only buttons are clickable, not the entire row
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            // Only allow clicks on buttons, links, or form elements
            if (!e.target.closest('a, button, form, input, select')) {
                e.stopPropagation();
                e.preventDefault();
            }
        });
        
        // Make sure all buttons and links stop propagation
        row.querySelectorAll('a, button, form').forEach(element => {
            element.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
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

/* PPR Badge Design */
.ppr-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.ppr-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
}

/* Modern Pagination Design */
.pagination-modern {
    display: flex;
    gap: 0.25rem;
    margin: 0;
}

.pagination-modern .page-item {
    margin: 0;
}

.pagination-modern .page-link {
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    color: #495057;
    font-weight: 500;
    transition: all 0.3s ease;
    background: white;
    text-decoration: none;
}

.pagination-modern .page-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.pagination-modern .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.pagination-modern .page-item.disabled .page-link {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

.pagination-modern .page-item.disabled .page-link:hover {
    transform: none;
    box-shadow: none;
    background: #f8f9fa;
    color: #6c757d;
    border-color: #dee2e6;
}

/* Loading state */
#tableContainer {
    transition: opacity 0.3s ease;
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

</style>
@endpush

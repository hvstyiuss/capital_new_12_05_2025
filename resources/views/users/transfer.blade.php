@extends('layouts.app')

@section('title', 'Transférer l\'Agent')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-exchange-alt me-2 text-success"></i>
                    Transférer l'Agent
                </h1>
                <p class="text-muted mb-0">Transférer {{ $user->name }} ({{ $user->ppr }}) vers une nouvelle entité</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('hr.users.show', $user) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>Voir
                </a>
                <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-exchange-alt me-2 text-success"></i>
                        Transfert d'Entité
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('hr.users.transfer.store', $user) }}" method="POST" id="transferForm" novalidate>
                        @csrf
                        
                        <!-- Entity Selection -->
                        <div class="mb-4">
                            <label for="entity_search" class="form-label fw-semibold mb-2">
                                <i class="fas fa-search me-2 text-primary"></i>
                                Rechercher une entité
                            </label>
                            
                            <!-- Search Input -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="entity_search" 
                                    placeholder="Rechercher une entité..." 
                                    class="form-control">
                            </div>
                            
                            <label for="to_entite_id" class="form-label fw-semibold">
                                Entité de Destination <span class="text-danger">*</span>
                            </label>
                            <select 
                                id="to_entite_id" 
                                name="to_entite_id" 
                                class="form-select @error('to_entite_id') is-invalid @enderror"
                                size="10"
                                required>
                                <option value="">Sélectionnez une entité</option>
                                @foreach($entites as $entite)
                                    <option value="{{ $entite->id }}" 
                                            data-name="{{ $entite->full_text ?? strtolower($entite->display_name ?? $entite->name) }}"
                                            data-has-chef="{{ $entite->chef_ppr ? '1' : '0' }}"
                                            {{ old('to_entite_id') == $entite->id ? 'selected' : '' }}>
                                        {{ $entite->display_name ?? $entite->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_entite_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transfer Date -->
                        <div class="mb-4">
                            <label for="date_debut" class="form-label fw-semibold">
                                Date de début d'affectation <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('date_debut') is-invalid @enderror" 
                                   id="date_debut" 
                                   name="date_debut" 
                                   value="{{ old('date_debut', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>La date doit être aujourd'hui ou une date future
                            </small>
                        </div>

                        <!-- Rôle dans l'entité -->
                        <div class="mb-4">
                            <label for="role_in_entity" class="form-label fw-semibold">
                                Rôle dans l'entité <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role_in_entity') is-invalid @enderror" 
                                    id="role_in_entity" 
                                    name="role_in_entity" 
                                    required>
                                <option value="">Sélectionnez un rôle</option>
                                <option value="collaborateur" {{ old('role_in_entity', $defaultRole ?? 'collaborateur') == 'collaborateur' ? 'selected' : '' }}>
                                    Collaborateur
                                </option>
                                <option value="chef" {{ old('role_in_entity', $defaultRole ?? 'collaborateur') == 'chef' ? 'selected' : '' }}>
                                    Chef
                                </option>
                            </select>
                            @error('role_in_entity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Choisissez si l'agent sera un collaborateur ou un chef de l'entité
                            </small>
                        </div>

                        <!-- Poste (optional, for additional details) -->
                        <div class="mb-4">
                            <label for="poste" class="form-label fw-semibold">Poste (optionnel)</label>
                            <input type="text" 
                                   class="form-control @error('poste') is-invalid @enderror" 
                                   id="poste" 
                                   name="poste" 
                                   value="{{ old('poste', $currentParcours->poste ?? '') }}"
                                   placeholder="Ex: Spécialiste, Agent, Technicien, etc.">
                            @error('poste')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Précision supplémentaire sur le poste (optionnel)
                            </small>
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <label for="reason" class="form-label fw-semibold">Raison du transfert</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" 
                                      name="reason" 
                                      rows="3"
                                      placeholder="Ex: Transfert administratif, Mutation, etc.">{{ old('reason', 'Transfert administratif') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                            <a href="{{ route('hr.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-exchange-alt me-2"></i>Confirmer le Transfert
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Info Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-user me-2 text-primary"></i>
                        Informations Agent
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small mb-1 d-block">Nom complet</label>
                        <p class="fw-bold mb-0">{{ $user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1 d-block">PPR</label>
                        <p class="fw-bold mb-0">{{ $user->ppr }}</p>
                    </div>
                    @if($user->email)
                    <div class="mb-3">
                        <label class="text-muted small mb-1 d-block">Email</label>
                        <p class="fw-bold mb-0">{{ $user->email }}</p>
                    </div>
                    @endif
                    @if($currentParcours && $currentParcours->grade)
                    <div class="mb-3">
                        <label class="text-muted small mb-1 d-block">Grade actuel</label>
                        <p class="fw-bold mb-0">{{ $currentParcours->grade->name }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const entitySearch = document.getElementById('entity_search');
    const entitySelect = document.getElementById('to_entite_id');
    const roleSelect = document.getElementById('role_in_entity');
    const options = Array.from(entitySelect.options);
    const entitiesWithChefs = @json($entitiesWithChefs);

    // Entity search functionality
    if (entitySearch) {
        entitySearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                } else {
                    const optionText = option.getAttribute('data-name') || option.text.toLowerCase();
                    if (optionText.includes(searchTerm)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
        });
    }

    // Check if selected entity has a chef and disable "chef" option
    function checkEntityChef() {
        const selectedEntityId = entitySelect.value;
        if (!selectedEntityId) {
            // Reset if no entity selected
            const chefOption = roleSelect.querySelector('option[value="chef"]');
            if (chefOption) {
                chefOption.disabled = false;
            }
            return;
        }

        const selectedOption = entitySelect.options[entitySelect.selectedIndex];
        const hasChef = selectedOption && selectedOption.getAttribute('data-has-chef') === '1';
        
        // Check if current user is the chef of the selected entity
        const currentUserPpr = '{{ $user->ppr }}';
        const entityChefPpr = entitiesWithChefs[selectedEntityId];
        const currentUserIsChef = entityChefPpr && entityChefPpr === currentUserPpr;
        
        if (hasChef && !currentUserIsChef) {
            // Disable "chef" option
            const chefOption = roleSelect.querySelector('option[value="chef"]');
            if (chefOption) {
                chefOption.disabled = true;
                // If "chef" is currently selected, change to "collaborateur"
                if (roleSelect.value === 'chef') {
                    roleSelect.value = 'collaborateur';
                }
            }
        } else {
            // Enable "chef" option
            const chefOption = roleSelect.querySelector('option[value="chef"]');
            if (chefOption) {
                chefOption.disabled = false;
            }
        }
    }

    // Listen for entity selection changes
    if (entitySelect) {
        entitySelect.addEventListener('change', checkEntityChef);
        // Check on page load if entity is pre-selected
        checkEntityChef();
    }

    // Form validation - prevent duplicate messages
    const transferForm = document.getElementById('transferForm');
    if (transferForm) {
        // Remove any duplicate error messages on load
        function removeDuplicateErrors() {
            const errorMessages = transferForm.querySelectorAll('.invalid-feedback, .is-invalid');
            const seen = new Set();
            errorMessages.forEach(error => {
                const field = error.closest('.mb-4') || error.previousElementSibling;
                const key = field ? field.id || field.className : '';
                if (seen.has(key)) {
                    error.remove();
                } else {
                    seen.add(key);
                }
            });
        }
        
        // Run on load
        removeDuplicateErrors();
        
        transferForm.addEventListener('submit', function(e) {
            // Clear previous validation states
            transferForm.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            transferForm.querySelectorAll('.invalid-feedback').forEach(msg => {
                msg.remove();
            });
            
            let isValid = true;
            const toEntiteId = document.getElementById('to_entite_id');
            const dateDebut = document.getElementById('date_debut');
            
            // Validate entity selection
            if (!toEntiteId.value) {
                isValid = false;
                toEntiteId.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Veuillez sélectionner une entité de destination.';
                toEntiteId.parentElement.appendChild(errorDiv);
            }
            
            // Validate date
            if (!dateDebut.value) {
                isValid = false;
                dateDebut.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Veuillez sélectionner une date de début.';
                dateDebut.parentElement.appendChild(errorDiv);
            } else {
                const today = new Date().toISOString().split('T')[0];
                if (dateDebut.value < today) {
                    isValid = false;
                    dateDebut.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'La date de début doit être aujourd\'hui ou une date future.';
                    dateDebut.parentElement.appendChild(errorDiv);
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = transferForm.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                return false;
            }

            // Confirm before submitting
            if (!confirm('Êtes-vous sûr de vouloir transférer cet agent vers la nouvelle entité ?')) {
                e.preventDefault();
                return false;
            }
        });
    }

});
</script>
@endpush

@push('styles')
<style>
.form-select[size] {
    min-height: 300px;
    font-size: 0.9rem;
}

.form-select option {
    padding: 0.5rem;
}

.card {
    border-radius: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.btn-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
}

/* Prevent duplicate validation messages */
.invalid-feedback {
    display: block;
}

.mb-4 .invalid-feedback:not(:first-of-type) {
    display: none !important;
}

/* Hide browser validation messages */
input:invalid,
select:invalid,
textarea:invalid {
    box-shadow: none !important;
}

input:invalid:focus,
select:invalid:focus,
textarea:invalid:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
}
</style>
@endpush


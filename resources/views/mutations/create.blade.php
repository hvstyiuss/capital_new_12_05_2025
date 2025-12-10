@extends('layouts.app')

@section('title', 'Nouvelle Mutation')

@section('content')
<div class="container-fluid py-4">
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Tableau de Bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('mutations.tracking') }}" class="text-decoration-none">Mutations</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nouvelle Mutation</li>
                        </ol>
                    </nav>
                    <h1 class="h2 mb-2 fw-bold text-dark">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>
                        Faire une Demande de Mutation
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Créer une nouvelle demande de mutation
                    </p>
                </div>
                <a href="{{ route('mutations.tracking') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-file-alt text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Formulaire de Demande</h5>
                            <small class="text-muted">Remplissez les informations ci-dessous pour soumettre votre demande</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5">
                    <form id="mutationRequestForm" novalidate data-skip-loading="true">
                        <!-- User Info (Hidden) -->
                        <input type="hidden" name="ppr" value="{{ auth()->user()->ppr }}">

                        <!-- Mutation Type Selection -->
                        <div class="mb-5">
                            <label class="form-label fw-bold mb-3 fs-5">
                                <i class="fas fa-exchange-alt me-2 text-primary"></i>
                                Type de Mutation <span class="text-danger">*</span>
                            </label>
                            <div class="row g-4 justify-content-center" id="mutation_type_container">
                                @if($hasInternalEntities)
                                <div class="col-md-5 col-lg-4" id="interne_container">
                                    <div class="card border-3 h-100 mutation-type-card cursor-pointer position-relative overflow-hidden" data-type="interne" style="border-radius: 16px;">
                                        <div class="position-absolute top-0 end-0 bg-primary bg-opacity-5" style="width: 100px; height: 100px; border-radius: 0 0 0 100px;"></div>
                                        <div class="card-body text-center p-4 position-relative">
                                            <div class="form-check d-flex flex-column align-items-center">
                                                <input type="radio" name="mutation_type" value="interne" id="mutation_type_interne" class="form-check-input" style="width: 24px; height: 24px; margin-top: 0;" {{ $hasInternalEntities ? 'checked' : '' }}>
                                                <label class="form-check-label mt-3 w-100" for="mutation_type_interne">
                                                    <div class="mb-3">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                            <i class="fas fa-building text-primary" style="font-size: 2rem;"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Interne</h5>
                                                    <p class="text-muted mb-3 small">Dans la même direction</p>
                                                    <span id="interne_count" class="badge bg-primary px-3 py-2 fs-6"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-5 col-lg-4" id="externe_container">
                                    <div class="card border-3 h-100 mutation-type-card cursor-pointer position-relative overflow-hidden" data-type="externe" style="border-radius: 16px;">
                                        <div class="position-absolute top-0 end-0 bg-success bg-opacity-5" style="width: 100px; height: 100px; border-radius: 0 0 0 100px;"></div>
                                        <div class="card-body text-center p-4 position-relative">
                                            <div class="form-check d-flex flex-column align-items-center">
                                                <input type="radio" name="mutation_type" value="externe" id="mutation_type_externe" class="form-check-input" style="width: 24px; height: 24px; margin-top: 0;" {{ !$hasInternalEntities ? 'checked' : '' }}>
                                                <label class="form-check-label mt-3 w-100" for="mutation_type_externe">
                                                    <div class="mb-3">
                                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                            <i class="fas fa-exchange-alt text-success" style="font-size: 2rem;"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Externe</h5>
                                                    <p class="text-muted mb-3 small">Vers une autre direction</p>
                                                    <span id="externe_count" class="badge bg-success px-3 py-2 fs-6"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-info-circle me-1"></i>Sélectionnez le type de mutation souhaité.
                            </small>
                        </div>

                        <!-- Entity Selection -->
                        <div class="mb-5">
                            <label for="to_entite_id" class="form-label fw-bold mb-3 fs-5">
                                <i class="fas fa-building me-2 text-primary"></i>
                                Entité de Destination <span class="text-danger">*</span>
                            </label>
                            
                            <!-- Search Input -->
                            <div class="input-group input-group-lg mb-3 shadow-sm" style="border-radius: 12px;">
                                <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="entity_search" 
                                    placeholder="Rechercher une entité..." 
                                    class="form-control border-0"
                                    style="border-radius: 0 12px 12px 0;">
                            </div>
                            
                            <div class="position-relative">
                                <select 
                                    id="to_entite_id" 
                                    name="to_entite_id" 
                                    class="form-select form-select-lg @error('to_entite_id') is-invalid @enderror shadow-sm"
                                    size="8"
                                    style="border-radius: 12px; min-height: 250px;">
                                    <option value="">Sélectionnez une entité</option>
                                    @foreach($entites as $entite)
                                        @php
                                            $entiteData = $entitesData->firstWhere('id', $entite->id);
                                            $isInternal = $entiteData['is_internal'] ?? false;
                                            $peopleCount = $entiteData['people_count'] ?? 0;
                                            $isCurrentEntity = $entiteData['is_current_entity'] ?? false;
                                            $displayName = $entite->name;
                                            if ($entite->entity_type) {
                                                $displayName .= ' (' . ucfirst($entite->entity_type) . ')';
                                            }
                                            $fullText = strtolower($displayName);
                                            $peopleText = $peopleCount . ' personne' . ($peopleCount > 1 ? 's' : '');
                                        @endphp
                                        <option value="{{ $entite->id }}" 
                                                data-is-internal="{{ $isInternal ? 'true' : 'false' }}"
                                                data-is-current="{{ $isCurrentEntity ? 'true' : 'false' }}"
                                                data-name="{{ $fullText }}"
                                                data-full-text="{{ $fullText }}"
                                                data-people-text="{{ $peopleText }}"
                                                {{ old('to_entite_id') == $entite->id ? 'selected' : '' }}>
                                            {{ $displayName }} - {{ $peopleText }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="to_entite_id_error" class="invalid-feedback d-none"></div>
                                @error('to_entite_id')
                                    <div class="text-danger small mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-info-circle me-1"></i>Sélectionnez l'entité vers laquelle vous souhaitez être muté(e).
                            </small>
                        </div>

                        <!-- Motif Selection -->
                        <div class="mb-5">
                            <label for="motif" class="form-label fw-bold mb-3 fs-5">
                                <i class="fas fa-file-alt me-2 text-primary"></i>
                                Motif <span class="text-danger">*</span>
                            </label>
                            <select 
                                id="motif" 
                                name="motif" 
                                class="form-select form-select-lg @error('motif') is-invalid @enderror shadow-sm"
                                style="border-radius: 12px;">
                                <option value="">Sélectionnez un motif</option>
                                <option value="Promotion" {{ old('motif') == 'Promotion' ? 'selected' : '' }}>Promotion</option>
                                <option value="Mutation professionnelle" {{ old('motif') == 'Mutation professionnelle' ? 'selected' : '' }}>Mutation professionnelle</option>
                                <option value="Rapprochement familial" {{ old('motif') == 'Rapprochement familial' ? 'selected' : '' }}>Rapprochement familial</option>
                                <option value="Raison personnelle" {{ old('motif') == 'Raison personnelle' ? 'selected' : '' }}>Raison personnelle</option>
                                <option value="Développement de carrière" {{ old('motif') == 'Développement de carrière' ? 'selected' : '' }}>Développement de carrière</option>
                                <option value="Autre" {{ old('motif') == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            <div id="motif_error" class="invalid-feedback d-none"></div>
                            @error('motif')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-info-circle me-1"></i>Sélectionnez le motif de votre demande de mutation.
                            </small>
                            
                            <!-- Conditional input for "Autre" -->
                            <div id="motif_autre_container" class="mt-4" style="display: none;">
                                <label for="motif_autre" class="form-label fw-bold mb-2">
                                    <i class="fas fa-edit me-2 text-primary"></i>
                                    Précisez le motif <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="motif_autre" 
                                    name="motif_autre" 
                                    class="form-control form-control-lg @error('motif_autre') is-invalid @enderror shadow-sm"
                                    value="{{ old('motif_autre') }}"
                                    placeholder="Veuillez préciser le motif de votre demande..."
                                    style="border-radius: 12px;">
                                <div id="motif_autre_error" class="invalid-feedback d-none"></div>
                                @error('motif_autre')
                                    <div class="text-danger small mt-2 motif_autre_laravel_error">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center gap-3 mt-5 pt-4 border-top">
                            <a href="{{ route('mutations.tracking') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg px-5 shadow-sm" data-skip-global-loading="true" style="border-radius: 12px;">
                                <i class="fas fa-paper-plane me-2" id="submitIcon"></i>
                                <span id="submitText">Soumettre la Demande</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.mutation-type-card {
    transition: all 0.3s ease;
    border-color: #dee2e6 !important;
    background: #fff;
}

.mutation-type-card:hover {
    border-color: #0d6efd !important;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-4px);
}

.mutation-type-card input[type="radio"]:checked ~ label,
.mutation-type-card:has(input[type="radio"]:checked) {
    border-color: #0d6efd !important;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(13, 110, 253, 0.02) 100%);
}

.mutation-type-card[data-type="externe"]:has(input[type="radio"]:checked) {
    border-color: #198754 !important;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.05) 0%, rgba(25, 135, 84, 0.02) 100%);
}

.mutation-type-card input[type="radio"] {
    margin-top: 0;
}

.cursor-pointer {
    cursor: pointer;
}

.form-select[size] {
    min-height: 250px;
    font-size: 1rem;
}

.form-select option {
    padding: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
}

.form-select option:hover {
    background-color: #f8f9fa;
}

.form-select option:checked {
    background-color: #0d6efd;
    color: white;
}

/* Normal font for entity selection */
#to_entite_id {
    font-family: inherit;
    font-size: 1rem;
}

#to_entite_id option {
    font-family: inherit;
    white-space: normal;
    direction: ltr;
    padding: 0.75rem;
}

.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item a {
    color: #6c757d;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: #0d6efd;
}

.btn-lg {
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 600;
}

.btn-lg:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card {
    border-radius: 16px;
    overflow: hidden;
}

.form-control-lg,
.form-select-lg {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

.input-group-lg .form-control {
    border-radius: 12px;
}

@media (max-width: 768px) {
    .mutation-type-card {
        margin-bottom: 1rem;
    }
    
    .btn-lg {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mutationRequestForm');
    const selectField = document.getElementById('to_entite_id');
    const errorDiv = document.getElementById('to_entite_id_error');
    const mutationTypeInterne = document.getElementById('mutation_type_interne');
    const mutationTypeExterne = document.getElementById('mutation_type_externe');
    const entitySearch = document.getElementById('entity_search');
    const interneCountSpan = document.getElementById('interne_count');
    const externeCountSpan = document.getElementById('externe_count');
    const interneContainer = document.getElementById('interne_container');
    
    // Early return if essential elements are missing
    if (!form || !selectField) {
        console.error('Essential form elements not found');
        return;
    }
    
    // Store all options and format them with right-aligned person count
    const allOptions = Array.from(selectField.options);
    const defaultOption = allOptions[0]; // "Sélectionnez une entité"
    
    // Format options with right-aligned person count
    function formatOptionText(option) {
        if (!option.value || !option.getAttribute('data-people-text')) {
            return option.textContent; // Return as-is for default option
        }
        
        // Get display name from original text (before " - ")
        const originalText = option.getAttribute('data-original-text');
        let displayName = '';
        
        if (originalText) {
            // Extract display name from original text (before " - ")
            const parts = originalText.split(' - ');
            if (parts.length > 0) {
                displayName = parts[0];
            }
        }
        
        // If still empty, try to reconstruct from current text
        if (!displayName) {
            const currentText = option.textContent;
            const textParts = currentText.split(' - ');
            if (textParts.length > 0) {
                displayName = textParts[0].trim();
            }
        }
        
        const peopleText = option.getAttribute('data-people-text');
        const separator = ' - '; // Clear separator between name and person count
        
        // Calculate padding to align person count (using spaces)
        const maxWidth = 75; // Approximate character width for alignment
        const nameWidth = displayName.length;
        const separatorWidth = separator.length;
        const peopleWidth = peopleText.length;
        const padding = Math.max(0, maxWidth - nameWidth - separatorWidth - peopleWidth);
        
        return displayName + separator + ' '.repeat(padding) + peopleText;
    }
    
    // Format all options on page load and store original text
    allOptions.slice(1).forEach(option => {
        if (option.value) {
            // Store original text BEFORE any formatting (for search) - keep original case
            // The original text from PHP includes: "Entity Name - 2 personnes"
            const originalText = option.textContent.trim();
            
            // Store both original case and lowercase versions
            option.setAttribute('data-original-text', originalText);
            option.setAttribute('data-original-text-lower', originalText.toLowerCase());
            
            // Format the option text with right-aligned person count if it has people text
            if (option.getAttribute('data-people-text')) {
                // Format AFTER storing original text
                option.textContent = formatOptionText(option);
            }
        }
    });
    
    // Calculate and display counts
    function updateCounts() {
        let interneCount = 0;
        let externeCount = 0;
        
        allOptions.slice(1).forEach(option => {
            const isInternal = option.getAttribute('data-is-internal') === 'true' || option.getAttribute('data-is-internal') === '1';
            const isCurrent = option.getAttribute('data-is-current') === 'true' || option.getAttribute('data-is-current') === '1';
            
            if (isInternal && !isCurrent) {
                interneCount++;
            } else if (!isInternal) {
                externeCount++;
            }
        });
        
        const interneContainer = document.getElementById('interne_container');
        const interneCountSpan = document.getElementById('interne_count');
        const externeContainer = document.getElementById('externe_container');
        
        // If no internal entities, hide interne option completely and auto-select externe
        if (interneCount === 0) {
            if (interneContainer) {
                interneContainer.style.display = 'none';
                interneContainer.remove(); // Remove from DOM completely
            }
            // Auto-select externe if interne is currently selected
            if (mutationTypeInterne && mutationTypeInterne.checked) {
                mutationTypeExterne.checked = true;
                mutationTypeInterne.checked = false;
                // Trigger change event to update filters
                mutationTypeExterne.dispatchEvent(new Event('change'));
            }
            // Keep externe container at same width but centered
            if (externeContainer) {
                externeContainer.className = 'col-md-5 col-lg-4';
            }
        } else {
            // Show interne container if it exists
            if (interneContainer) {
                interneContainer.style.display = 'block';
                if (interneCountSpan) {
                    interneCountSpan.textContent = `${interneCount} entité${interneCount > 1 ? 's' : ''}`;
                }
            }
            // Reset externe container to same width
            if (externeContainer) {
                externeContainer.className = 'col-md-5 col-lg-4';
            }
        }
        
        // Always update externe count
        if (externeCountSpan) {
            externeCountSpan.textContent = `${externeCount} entité${externeCount > 1 ? 's' : ''}`;
        }
    }
    
    // Function to filter entities based on mutation type and search
    function filterEntities() {
        const selectedType = mutationTypeInterne && mutationTypeInterne.checked ? 'interne' : 'externe';
        const currentValue = selectField.value;
        const searchTerm = (entitySearch ? entitySearch.value : '').toLowerCase().trim();
        
        // Clear all options except the default one
        selectField.innerHTML = '';
        selectField.appendChild(defaultOption.cloneNode(true));
        
        // Add filtered options
        allOptions.slice(1).forEach(option => {
            const isInternal = option.getAttribute('data-is-internal') === 'true' || option.getAttribute('data-is-internal') === '1';
            const isCurrent = option.getAttribute('data-is-current') === 'true' || option.getAttribute('data-is-current') === '1';
            
            // Filter by type first
            let shouldInclude = false;
            if (selectedType === 'interne') {
                // For interne: show only internal entities, exclude current entity
                shouldInclude = isInternal && !isCurrent;
            } else {
                // For externe: show only external entities
                shouldInclude = !isInternal;

                // In externe mode, exclude top management entities like Directeur Général & Secrétaire Général
                if (shouldInclude) {
                    const nameLower =
                        (option.getAttribute('data-name') ||
                         option.getAttribute('data-original-text-lower') ||
                         option.textContent
                        ).toLowerCase();

                    if (
                        nameLower.includes('directeur général') ||
                        nameLower.includes('secrétaire général')
                    ) {
                        shouldInclude = false;
                    }
                }
            }
            
            // If type filter passed, then check search term
            if (shouldInclude) {
                // If there's a search term, filter by it
                if (searchTerm && searchTerm.length > 0) {
                    // Get the searchable text - prefer original text, fallback to current
                    let searchableText = '';
                    
                    // First try to get original text (before formatting)
                    const originalTextLower = option.getAttribute('data-original-text-lower');
                    if (originalTextLower) {
                        searchableText = originalTextLower;
                    } else {
                        // Fallback: use current text and normalize it
                        // Remove extra spaces from formatting and normalize
                        searchableText = option.textContent.toLowerCase().replace(/\s+/g, ' ').trim();
                    }
                    
                    // Also check the entity name attribute
                    const optionName = (option.getAttribute('data-name') || '').toLowerCase();
                    
                    // Search in both the full text and the entity name
                    const matchesFullText = searchableText && searchableText.includes(searchTerm);
                    const matchesEntityName = optionName && optionName.includes(searchTerm);
                    
                    // Only include if search term matches in at least one location
                    shouldInclude = matchesFullText || matchesEntityName;
                }
                // If no search term, include all that passed type filter
            }
            
            if (shouldInclude) {
                const clonedOption = option.cloneNode(true);
                // Ensure cloned option has the original text stored
                let originalTextLower = clonedOption.getAttribute('data-original-text-lower');
                if (!originalTextLower) {
                    const originalText = clonedOption.getAttribute('data-original-text');
                    if (originalText) {
                        originalTextLower = originalText.toLowerCase();
                        clonedOption.setAttribute('data-original-text-lower', originalTextLower);
                    }
                }
                // Format the cloned option text with right-aligned person count
                if (clonedOption.value && clonedOption.getAttribute('data-people-text')) {
                    clonedOption.textContent = formatOptionText(clonedOption);
                }
                selectField.appendChild(clonedOption);
            }
        });
        
        // Restore selection if still valid
        if (currentValue) {
            const optionExists = Array.from(selectField.options).some(opt => opt.value === currentValue);
            if (optionExists) {
                selectField.value = currentValue;
            } else {
                selectField.value = '';
            }
        }
    }
    
    // Click on card to select radio
    const mutationTypeCards = document.querySelectorAll('.mutation-type-card');
    if (mutationTypeCards.length > 0) {
        mutationTypeCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }
            });
        });
    }
    
    // Filter on mutation type change (only if elements exist)
    if (mutationTypeInterne) {
        mutationTypeInterne.addEventListener('change', filterEntities);
    }
    if (mutationTypeExterne) {
        mutationTypeExterne.addEventListener('change', filterEntities);
    }
    
    // Filter on search input - immediate filtering for better UX
    if (entitySearch) {
        entitySearch.addEventListener('input', function(e) {
            e.stopPropagation();
            filterEntities();
        });
        
        // Also filter on paste events
        entitySearch.addEventListener('paste', function() {
            setTimeout(filterEntities, 10);
        });
        
        // Filter on keyup for additional responsiveness (backup)
        entitySearch.addEventListener('keyup', function(e) {
            // Filter on any key except navigation keys
            if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes(e.key)) {
                filterEntities();
            }
        });
        
        // Clear search and reset filter when search is cleared
        entitySearch.addEventListener('search', function() {
            filterEntities();
        });
    }
    
    // Initial filter and counts - run after DOM is ready
    // First check if interne container should be hidden based on server-side data
    const hasInternalEntities = {{ $hasInternalEntities ? 'true' : 'false' }};
    if (!hasInternalEntities && interneContainer) {
        interneContainer.style.display = 'none';
        interneContainer.remove(); // Remove from DOM completely
        if (mutationTypeInterne && mutationTypeInterne.checked) {
            mutationTypeExterne.checked = true;
            mutationTypeInterne.checked = false;
        }
        if (externeContainer) {
            externeContainer.className = 'col-md-5 col-lg-4';
        }
    }
    
    // Then update counts and filter
    updateCounts();
    filterEntities();
    
    // Initialize form validation using utility
    if (form && window.FormUtils) {
        FormUtils.initFormValidation(form);
    }
    
    // Get motif_autre field reference (declare once)
    const motifAutreField = document.getElementById('motif_autre');
    const motifAutreContainer = document.getElementById('motif_autre_container');
    const motifAutreErrorDiv = document.getElementById('motif_autre_error');
    
    // Specifically handle motif_autre field to prevent duplicate errors
    if (motifAutreField && window.FormUtils) {
        motifAutreField.addEventListener('invalid', function(e) {
            e.preventDefault();
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
            // Hide Laravel error
            const laravelError = document.querySelector('.motif_autre_laravel_error');
            if (laravelError) {
                laravelError.style.display = 'none';
            }
        });
    }
    
    // Custom validation on submit
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Hide all Laravel error messages first
            if (window.FormUtils) {
                FormUtils.hideLaravelErrors(['.text-danger.small', '.motif_autre_laravel_error']);
            }
            
            // Clear previous errors
            if (window.FormUtils) {
                FormUtils.clearFieldError(selectField, errorDiv);
            }
            
            // Validate mutation type
            const mutationTypeRadios = [mutationTypeInterne, mutationTypeExterne].filter(r => r);
            if (!window.FormUtils || !FormUtils.validateRadioGroup(mutationTypeRadios, errorDiv, 'Veuillez sélectionner un type de mutation')) {
                if (selectField) {
                    selectField.classList.add('is-invalid');
                }
                return false;
            }
            
            // Validate motif
            const motifField = document.getElementById('motif');
            const motifErrorDiv = document.getElementById('motif_error');
            
            if (!motifField) {
                return false;
            }
            
            if (!window.FormUtils || !FormUtils.validateRequired(motifField, motifErrorDiv)) {
                return false;
            }
            
            // If "Autre" is selected, validate the additional input
            if (motifField.value === 'Autre') {
                if (window.FormUtils) {
                    FormUtils.clearFieldError(motifAutreField, motifAutreErrorDiv);
                    FormUtils.hideLaravelErrors(['.motif_autre_laravel_error']);
                }
                
                if (!window.FormUtils || !FormUtils.validateRequired(motifAutreField, motifAutreErrorDiv, 'Veuillez préciser le motif')) {
                    return false;
                }
            }
            
            // Validate entity selection
            const toEntiteId = selectField.value.trim();
            if (!window.FormUtils || !FormUtils.validateRequired(selectField, errorDiv)) {
                if (selectField) {
                    selectField.focus();
                }
                return false;
            }
            
            // Prepare form data
            const pprInput = form.querySelector('input[name="ppr"]');
            const formData = {
                ppr: pprInput ? pprInput.value : '',
                to_entite_id: parseInt(toEntiteId),
                mutation_type: (mutationTypeInterne && mutationTypeInterne.checked) ? 'interne' : 'externe',
                motif: motifField.value === 'Autre' ? (motifAutreField ? motifAutreField.value : '') : motifField.value,
            };
            
            // Submit using utility
            const submitBtn = document.getElementById('submitBtn');
            const submitIcon = document.getElementById('submitIcon');
            
            const fieldMap = {
                'to_entite_id': { field: selectField, errorDiv: errorDiv },
                'motif': { field: motifField, errorDiv: motifErrorDiv },
                'motif_autre': { field: motifAutreField, errorDiv: motifAutreErrorDiv }
            };
            
            const result = await FormUtils.submitForm(
                '/api/mutations',
                'POST',
                formData,
                {
                    submitBtn: submitBtn,
                    submitIcon: submitIcon,
                    onSuccess: function(data) {
                        window.location.href = '{{ route("mutations.tracking") }}';
                    },
                    onValidationError: function(errors) {
                        FormUtils.displayValidationErrors(errors, fieldMap);
                    }
                }
            );
        });
    }
    
    // Clear error on change
    if (selectField) {
        selectField.addEventListener('change', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                if (errorDiv) {
                    errorDiv.classList.add('d-none');
                    errorDiv.textContent = '';
                }
            }
        });
    }
    
    // Handle motif selection and show/hide "Autre" input
    // Note: motifAutreField, motifAutreContainer, and motifAutreErrorDiv are already declared above
    const motifField = document.getElementById('motif');
    const motifErrorDiv = document.getElementById('motif_error');
    
    // Function to clear all motif-related errors
    function clearMotifErrors() {
        if (motifField) {
            motifField.classList.remove('is-invalid');
        }
        if (motifErrorDiv) {
            motifErrorDiv.classList.add('d-none');
            motifErrorDiv.textContent = '';
        }
        // Hide Laravel error messages if they exist
        if (motifField && motifField.parentElement) {
            const laravelError = motifField.parentElement.querySelector('.text-danger.small');
            if (laravelError) {
                laravelError.style.display = 'none';
            }
        }
    }
    
    if (motifField) {
        motifField.addEventListener('change', function() {
            // Clear errors
            clearMotifErrors();
            
            // Show/hide "Autre" input
            if (this.value === 'Autre') {
                if (motifAutreContainer) {
                    motifAutreContainer.style.display = 'block';
                }
                // Don't set required attribute - we'll validate with JavaScript
                if (motifAutreField) {
                    motifAutreField.removeAttribute('required');
                }
            } else {
                if (motifAutreContainer) {
                    motifAutreContainer.style.display = 'none';
                }
                if (motifAutreField) {
                    motifAutreField.removeAttribute('required');
                    motifAutreField.value = '';
                    motifAutreField.classList.remove('is-invalid');
                }
                if (motifAutreErrorDiv) {
                    motifAutreErrorDiv.classList.add('d-none');
                    motifAutreErrorDiv.textContent = '';
                }
                // Hide Laravel error messages for motif_autre if they exist
                const laravelErrorAutre = document.querySelector('.motif_autre_laravel_error');
                if (laravelErrorAutre) {
                    laravelErrorAutre.style.display = 'none';
                }
            }
        });
    }
    
    // Clear "Autre" input error on change
    if (motifAutreField) {
        motifAutreField.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                if (motifAutreErrorDiv) {
                    motifAutreErrorDiv.classList.add('d-none');
                    motifAutreErrorDiv.textContent = '';
                }
                // Hide Laravel error messages if they exist
                const laravelError = document.querySelector('.motif_autre_laravel_error');
                if (laravelError) {
                    laravelError.style.display = 'none';
                }
            }
        });
        
        // Also clear on focus
        motifAutreField.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
            if (motifAutreErrorDiv) {
                motifAutreErrorDiv.classList.add('d-none');
                motifAutreErrorDiv.textContent = '';
            }
            const laravelError = document.querySelector('.motif_autre_laravel_error');
            if (laravelError) {
                laravelError.style.display = 'none';
            }
        });
    }
    
    // Initialize "Autre" field visibility on page load
    if (motifField && motifField.value === 'Autre') {
        if (motifAutreContainer) {
            motifAutreContainer.style.display = 'block';
        }
        if (motifAutreField) {
            motifAutreField.removeAttribute('required');
        }
    }
});
</script>
@endpush
@endsection

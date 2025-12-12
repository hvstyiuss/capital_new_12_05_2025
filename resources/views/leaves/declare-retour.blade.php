@extends('layouts.app')

@section('title', 'Déclarer Date de Retour')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="bg-gradient-primary rounded-circle p-3 shadow-sm">
                <i class="fas fa-calendar-check text-white fs-4"></i>
            </div>
            <div>
                <h1 class="h3 mb-0 fw-bold text-dark">Déclarer Date de Retour</h1>
                <p class="text-muted mb-0 small">Déclarez votre date de retour pour vos demandes de congé approuvées</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if($successMessage)
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
        <i class="fas fa-check-circle me-2"></i>{{ $successMessage }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if($errorMessage)
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errorMessage }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($demandes->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Aucune demande approuvée disponible pour déclarer un retour.</p>
            </div>
        </div>
    @else
        <!-- Form to select demande and fill avis retour fields -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-file-alt me-2"></i>Nouvelle Déclaration de Retour
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('hr.leaves.store-declare-retour') }}" method="POST" id="declareRetourForm">
                    @csrf
                    
                    <!-- Select Demande -->
                    <div class="mb-4">
                        <label for="demande_id" class="form-label fw-semibold">
                            <i class="fas fa-list me-1"></i>Sélectionner une Demande <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('demande_id') is-invalid @enderror" id="demande_id" name="demande_id" required onchange="loadDemandeInfo(this.value)">
                            <option value="">-- Sélectionner une demande --</option>
        @foreach($demandes as $demande)
            @if(!$demande->hasAvisRetour)
                <option value="{{ $demande->id }}" 
                        data-avis-id="{{ $demande->avis ? $demande->avis->id : '' }}"
                        data-date-depart="{{ $demande->dateDepartInput }}"
                        data-date-retour-prevue="{{ $demande->dateRetourPrevueInput }}"
                        data-nb-jours-demandes="{{ $demande->nbJoursDemandes }}">
                    Demande #{{ $demande->id }} - 
                    Départ: {{ $demande->dateDepartFormatted }} | 
                    Retour prévu: {{ $demande->dateRetourPrevueFormatted }}
                </option>
            @endif
        @endforeach
                        </select>
                        <input type="hidden" name="avis_id" id="avis_id" value="">
                        @error('demande_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Demande Info (hidden by default) -->
                    <div id="demandeInfo" class="mb-4 d-none">
                        <div class="bg-light rounded p-3">
                            <h6 class="fw-semibold mb-3">Informations de la Demande</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Date de Départ</small>
                                    <span class="fw-bold" id="info_date_depart">-</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Date de Retour Prévue</small>
                                    <span class="fw-bold" id="info_date_retour_prevue">-</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Nombre de Jours Demandés</small>
                                    <span class="fw-bold" id="info_nb_jours">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date de Retour Déclarée -->
                    <div class="mb-4">
                        <label for="date_retour_declaree" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1"></i>Date de Retour Déclarée <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control @error('date_retour_declaree') is-invalid @enderror" 
                               id="date_retour_declaree" 
                               name="date_retour_declaree" 
                               value="{{ old('date_retour_declaree', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               required>
                        @error('date_retour_declaree')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date de Retour Effectif -->
                    <div class="mb-4">
                        <label for="date_retour_effectif" class="form-label fw-semibold">
                            <i class="fas fa-calendar-check me-1"></i>Date de Retour Effectif
                        </label>
                        <input type="date" 
                               class="form-control @error('date_retour_effectif') is-invalid @enderror" 
                               id="date_retour_effectif" 
                               name="date_retour_effectif" 
                               value="{{ old('date_retour_effectif', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}">
                        @error('date_retour_effectif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Nombre de Jours Consommés -->
                    <div class="mb-4">
                        <label for="nbr_jours_consumes" class="form-label fw-semibold">
                            <i class="fas fa-calendar-day me-1"></i>Nombre de Jours Consommés
                        </label>
                        <input type="number" 
                               class="form-control @error('nbr_jours_consumes') is-invalid @enderror" 
                               id="nbr_jours_consumes" 
                               name="nbr_jours_consumes" 
                               value="{{ old('nbr_jours_consumes', '') }}"
                               min="0"
                               placeholder="Calculé automatiquement si vide">
                        @error('nbr_jours_consumes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Laissez vide pour calculer automatiquement à partir des dates</small>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="{{ route('hr.leaves.annuel') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Déclarer la Date de Retour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function loadDemandeInfo(demandeId) {
    const select = document.getElementById('demande_id');
    const option = select.options[select.selectedIndex];
    
    if (!option || !option.value) {
        document.getElementById('demandeInfo').classList.add('d-none');
        document.getElementById('avis_id').value = '';
        return;
    }
    
    const avisId = option.getAttribute('data-avis-id');
    const dateDepart = option.getAttribute('data-date-depart');
    const dateRetourPrevue = option.getAttribute('data-date-retour-prevue');
    const nbJours = option.getAttribute('data-nb-jours-demandes');
    
    // Set avis_id
    document.getElementById('avis_id').value = avisId;
    
    // Update info display
    document.getElementById('info_date_depart').textContent = dateDepart ? formatDate(dateDepart) : 'N/A';
    document.getElementById('info_date_retour_prevue').textContent = dateRetourPrevue ? formatDate(dateRetourPrevue) : 'N/A';
    document.getElementById('info_nb_jours').textContent = nbJours ? nbJours + ' jour(s)' : 'N/A';
    
    // Show info section
    document.getElementById('demandeInfo').classList.remove('d-none');
    
    // Set default date_retour_declaree to today or date_retour_prevue (whichever is earlier)
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    let defaultDate = today;
    if (dateRetourPrevue) {
        const prevueDate = new Date(dateRetourPrevue);
        prevueDate.setHours(0, 0, 0, 0);
        if (prevueDate <= today) {
            defaultDate = prevueDate;
        }
    }
    
    // Ensure default date is not before departure date
    if (dateDepart) {
        const depDate = new Date(dateDepart);
        depDate.setHours(0, 0, 0, 0);
        if (defaultDate < depDate) {
            defaultDate = depDate;
        }
    }
    
    document.getElementById('date_retour_declaree').value = formatDateForInput(defaultDate);
    document.getElementById('date_retour_effectif').value = formatDateForInput(defaultDate);
    
    // Calculate nbr_jours_consumes if dates are available
    if (dateDepart && defaultDate) {
        const depDate = new Date(dateDepart);
        const retDate = new Date(defaultDate);
        
        // Check if same day
        const isSameDay = depDate.toDateString() === retDate.toDateString();
        
        if (isSameDay) {
            document.getElementById('nbr_jours_consumes').value = 0; // Same day = 0 days consumed
        } else {
            const diffTime = Math.abs(retDate - depDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both departure and return days
            document.getElementById('nbr_jours_consumes').value = diffDays;
        }
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
        
        function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
// Auto-calculate nbr_jours_consumes when dates change
document.getElementById('date_retour_declaree')?.addEventListener('change', function() {
    const select = document.getElementById('demande_id');
    const option = select.options[select.selectedIndex];
    
    if (!option || !option.value) return;
    
    const dateDepart = option.getAttribute('data-date-depart');
    const dateRetourDeclaree = this.value;
    
    if (dateDepart && dateRetourDeclaree) {
        const depDate = new Date(dateDepart);
        const retDate = new Date(dateRetourDeclaree);
        
        // Check if same day
        const isSameDay = depDate.toDateString() === retDate.toDateString();
        
        const nbrJoursInput = document.getElementById('nbr_jours_consumes');
        if (!nbrJoursInput.value || nbrJoursInput.value === '') {
            if (isSameDay) {
                nbrJoursInput.value = 0; // Same day = 0 days consumed
            } else {
                const diffTime = Math.abs(retDate - depDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                nbrJoursInput.value = diffDays;
            }
        }
    }
});

// Remove duplicate alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-success, .alert-danger');
    const seenMessages = new Set();
    
    alerts.forEach(function(alert) {
        const message = alert.textContent.trim();
        if (seenMessages.has(message)) {
            alert.remove();
        } else {
            seenMessages.add(message);
        }
    });
});
</script>
@endpush

@endsection

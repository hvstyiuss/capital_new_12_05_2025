@extends('layouts.app')

@section('title', 'Détails de la Demande de Congé')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center gap-3 mb-2">
                
                <div>
                    <h1 class="h3 mb-0 fw-bold text-dark">Détails de la Demande de Congé</h1>
                    <p class="text-muted mb-0 small">Demande #{{ $demande->id }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('hr.leaves.agents') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    @php
        $avis = $demande->avis;
        $avisDepart = $avis ? $avis->avisDepart : null;
        $avisRetour = $avis ? $avis->avisRetour : null;
        
        $statutMap = [
            'pending' => 'En attente',
            'approved' => 'Validé',
            'rejected' => 'Rejeté',
            'cancelled' => 'Annulé',
        ];
        
        $statutColors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
        ];
        
        $statutIcons = [
            'pending' => 'fa-clock',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'cancelled' => 'fa-ban',
        ];
    @endphp

    <div class="row g-4">
        <!-- Informations de la Demande -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="fas fa-info-circle text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Informations de la Demande</h5>
                            <p class="text-muted small mb-0">Détails généraux de la demande</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-hashtag me-1"></i>Nº Demande
                                </label>
                                <p class="h5 mb-0 fw-bold">#{{ $demande->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-user me-1"></i>Collaborateur
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-id-card me-1"></i>PPR
                                </label>
                                <p class="h6 mb-0 fw-bold">{{ $demande->ppr }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>Date de Dépôt
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $demande->created_at->format('d/m/Y') }}
                                </p>
                                <p class="text-muted small mb-0">{{ $demande->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avis de Départ -->
        @if($avisDepart)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                <i class="fas fa-plane-departure text-success"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Avis de Départ</h5>
                                <p class="text-muted small mb-0">Informations sur le départ en congé</p>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-{{ $statutColors[$avisDepart->statut] ?? 'secondary' }} px-3 py-2">
                                <i class="fas {{ $statutIcons[$avisDepart->statut] ?? 'fa-info-circle' }} me-1"></i>
                                {{ $statutMap[$avisDepart->statut] ?? $avisDepart->statut }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-check me-1"></i>Date de Départ
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $avisDepart->date_depart ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-times me-1"></i>Date de Retour
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-day me-1"></i>Nombre de Jours
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    <span class="badge bg-secondary px-3 py-2">{{ $avisDepart->nb_jours_demandes ?? 0 }} jour(s)</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-clock me-1"></i>Date de Dépôt
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $avisDepart->created_at->format('d/m/Y') }}
                                </p>
                                <p class="text-muted small mb-0">{{ $avisDepart->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        @if($avisDepart->pdf_path)
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('hr.leaves.download-avis-depart-pdf', $avisDepart->id) }}" 
                                   class="btn btn-outline-danger" 
                                   target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Télécharger l'Avis de Départ (PDF)
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($avisDepart->statut == 'pending')
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex gap-3 flex-wrap">
                            <form action="{{ route('hr.leaves.validate-avis-depart', $avisDepart->id) }}" method="POST" class="d-inline" id="approveForm">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-check me-2"></i>
                                    Approuver l'Avis de Départ
                                </button>
                            </form>
                            <form action="{{ route('hr.leaves.reject-avis-depart', $avisDepart->id) }}" method="POST" class="d-inline" id="rejectForm">
                                @csrf
                                <input type="hidden" name="rejection_reason" id="rejection_reason_depart_{{ $avisDepart->id }}" value="">
                                <button type="button" class="btn btn-danger btn-lg px-4" onclick="handleReject({{ $avisDepart->id }})">
                                    <i class="fas fa-times me-2"></i>
                                    Rejeter l'Avis de Départ
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Avis de Retour -->
        @if($avisRetour)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                <i class="fas fa-plane-arrival text-info"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Avis de Retour</h5>
                                <p class="text-muted small mb-0">Informations sur le retour de congé</p>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-{{ $statutColors[$avisRetour->statut] ?? 'secondary' }} px-3 py-2">
                                <i class="fas {{ $statutIcons[$avisRetour->statut] ?? 'fa-info-circle' }} me-1"></i>
                                {{ $statutMap[$avisRetour->statut] ?? $avisRetour->statut }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-check me-1"></i>Date de Retour Déclarée
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $avisRetour->date_retour_declaree ? \Carbon\Carbon::parse($avisRetour->date_retour_declaree)->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>Date de Retour Effectif
                                </label>
                                <p class="h6 mb-0 fw-bold" id="date_retour_effectif_display">
                                    {{ $avisRetour->date_retour_effectif ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('d/m/Y') : 'N/A' }}
                                </p>
                                <form id="form_update_date_retour_{{ $avisRetour->id }}" action="{{ route('hr.leaves.update-date-retour-effectif', $avisRetour->id) }}" method="POST" class="d-none mt-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group">
                                        <input type="date" name="date_retour_effectif" class="form-control" value="{{ $avisRetour->date_retour_effectif ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('Y-m-d') : '' }}" required>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="cancelEditDate({{ $avisRetour->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-calendar-day me-1"></i>Jours Consommés
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    <span class="badge bg-secondary px-3 py-2">{{ $avisRetour->nbr_jours_consumes ?? 0 }} jour(s)</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="info-box">
                                <label class="text-muted small text-uppercase fw-semibold mb-2 d-block">
                                    <i class="fas fa-clock me-1"></i>Date de Dépôt
                                </label>
                                <p class="h6 mb-0 fw-bold">
                                    {{ $avisRetour->created_at->format('d/m/Y') }}
                                </p>
                                <p class="text-muted small mb-0">{{ $avisRetour->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        @php
                            $hasPdfPath = isset($avisRetour->pdf_path) && $avisRetour->pdf_path && $avisRetour->pdf_path !== '';
                            $hasExplanationPdf = isset($avisRetour->explanation_pdf_path) && $avisRetour->explanation_pdf_path && $avisRetour->explanation_pdf_path !== '';
                            
                            // Check if explanation is needed (actual return date > declared return date)
                            $needsExplanation = false;
                            if ($avisRetour->date_retour_declaree && $avisRetour->date_retour_effectif) {
                                $dateRetourDeclaree = \Carbon\Carbon::parse($avisRetour->date_retour_declaree);
                                $dateRetourEffectif = \Carbon\Carbon::parse($avisRetour->date_retour_effectif);
                                $needsExplanation = $dateRetourEffectif->greaterThan($dateRetourDeclaree);
                            }
                        @endphp
                        @if($avisRetour->statut == 'approved')
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($hasPdfPath)
                                    <a href="{{ route('hr.leaves.download-avis-retour-pdf', $avisRetour->id) }}" 
                                       class="btn btn-outline-success" 
                                       target="_blank">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Télécharger l'Avis de Retour (PDF)
                                    </a>
                                    @else
                                    <a href="{{ route('hr.leaves.download-avis-retour-pdf', $avisRetour->id) }}" 
                                       class="btn btn-outline-success" 
                                       target="_blank"
                                       title="Le PDF sera généré automatiquement lors du téléchargement">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Télécharger l'Avis de Retour (PDF)
                                    </a>
                                    @endif
                                    @if($hasExplanationPdf || $needsExplanation)
                                    <a href="{{ route('hr.leaves.download-explanation-pdf', $avisRetour->id) }}" 
                                       class="btn btn-outline-danger" 
                                       target="_blank"
                                       title="{{ $hasExplanationPdf ? '' : 'Le PDF sera généré automatiquement lors du téléchargement' }}">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Télécharger la Note d'Explication (PDF)
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @elseif($hasExplanationPdf || $needsExplanation)
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('hr.leaves.download-explanation-pdf', $avisRetour->id) }}" 
                                   class="btn btn-outline-danger" 
                                   target="_blank"
                                   title="{{ $hasExplanationPdf ? '' : 'Le PDF sera généré automatiquement lors du téléchargement' }}">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Télécharger la Note d'Explication (PDF)
                                </a>
                            </div>
                        </div>
                        @endif
                        @if($avisRetour->explanation_required)
                        <div class="col-12">
                            <div class="alert alert-warning border-0 shadow-sm">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    <div class="flex-grow-1">
                                        <strong>Explication Requise:</strong> Retard détecté. 
                                        <br>
                                        <small>Délai: {{ \Carbon\Carbon::parse($avisRetour->explanation_deadline)->format('d/m/Y à H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($avisRetour->statut == 'pending')
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex gap-3 flex-wrap">
                            <form action="{{ route('hr.leaves.validate-avis-retour', $avisRetour->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg px-4" onclick="return confirm('Êtes-vous sûr de vouloir valider cet avis de retour?')">
                                    <i class="fas fa-check me-2"></i>
                                    Valider l'Avis de Retour
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="editDateRetourEffectif({{ $avisRetour->id }})">
                                <i class="fas fa-edit me-2"></i>
                                Modifier Date Retour
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .info-box {
        padding: 1rem;
        background: rgba(248, 250, 252, 0.5);
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .info-box:hover {
        background: rgba(248, 250, 252, 0.8);
        transform: translateY(-2px);
    }
    
    .card {
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .btn-lg {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@push('scripts')
<script>
function handleReject(avisDepartId) {
    if (!confirm('Êtes-vous sûr de vouloir rejeter cet avis de départ?')) {
        return;
    }
    
    var reason = prompt('Raison du rejet (optionnel - cliquez Annuler pour continuer sans raison):', '');
    document.getElementById('rejection_reason_depart_' + avisDepartId).value = reason || '';
    document.getElementById('rejectForm').submit();
}

function editDateRetourEffectif(avisRetourId) {
    const display = document.getElementById('date_retour_effectif_display');
    const form = document.getElementById('form_update_date_retour_' + avisRetourId);
    
    if (display && form) {
        display.parentElement.classList.add('d-none');
        form.classList.remove('d-none');
        form.querySelector('input[type="date"]').focus();
    }
}

function cancelEditDate(avisRetourId) {
    const display = document.getElementById('date_retour_effectif_display');
    const form = document.getElementById('form_update_date_retour_' + avisRetourId);
    
    if (display && form) {
        display.parentElement.classList.remove('d-none');
        form.classList.add('d-none');
    }
}

// Handle approve form submission - prevent duplicate confirmations
(function() {
    const approveForm = document.getElementById('approveForm');
    if (!approveForm) return;
    
    // Check if handler already attached
    if (approveForm.dataset.confirmHandlerAttached === 'true') {
        return;
    }
    
    // Mark as having handler attached
    approveForm.dataset.confirmHandlerAttached = 'true';
    
    let confirmationShown = false;
    
    approveForm.addEventListener('submit', function(e) {
        // Prevent duplicate confirmations
        if (confirmationShown) {
            // Already confirmed, allow submission
            return true;
        }
        
        // Show confirmation only once
        if (!confirm('Êtes-vous sûr de vouloir approuver cet avis de départ?')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
        
        // Mark confirmation as shown
        confirmationShown = true;
        
        // Allow form to submit
        return true;
    }, true); // Use capture phase to run before other handlers
})();

// Remove duplicate alerts - improved detection
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure all alerts are rendered
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-danger');
        const seenMessages = new Map();
        
        alerts.forEach(function(alert) {
            // Get clean message text (remove icons, buttons, and extra whitespace)
            const alertClone = alert.cloneNode(true);
            alertClone.querySelectorAll('.btn-close, i, button').forEach(el => el.remove());
            const message = alertClone.textContent.trim().replace(/\s+/g, ' ');
            
            if (message && seenMessages.has(message)) {
                // Remove duplicate alert (keep the first one, remove subsequent ones)
                alert.remove();
            } else if (message) {
                seenMessages.set(message, alert);
            }
        });
    }, 100);
});
</script>
@endpush

@endsection

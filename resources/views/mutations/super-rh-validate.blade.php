@extends('layouts.app')

@section('title', 'Valider la Mutation - Super Collaborateur Rh')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gradient-primary rounded-3 p-3 shadow-sm">
                <i class="fas fa-check-circle text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">
                    @if($isIntermediateReview)
                        Révision Intermédiaire de Mutation
                    @else
                        Validation de Mutation
                    @endif
                </h1>
                <p class="text-muted mb-0">
                    @if($isIntermediateReview)
                        Décider si la mutation doit être envoyée à la direction de destination
                    @else
                        Définir la date de début d'affectation
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Mutation Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-info-circle text-info"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Informations de la Mutation</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Agent</label>
                            <p class="mb-0 fw-semibold">
                                {{ $mutation->user ? ($mutation->user->fname . ' ' . $mutation->user->lname) : 'N/A' }}
                                <span class="badge bg-secondary ms-2">{{ $mutation->ppr }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Type de Mutation</label>
                            <p class="mb-0">
                                @if($mutation->mutation_type === 'interne')
                                    <span class="badge bg-info">Interne</span>
                                @elseif($mutation->mutation_type === 'externe')
                                    <span class="badge bg-warning text-dark">Externe</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Entité de Destination</label>
                            <p class="mb-0 fw-semibold">{{ $mutation->toEntite ? $mutation->toEntite->name : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Motif</label>
                            <p class="mb-0 fw-semibold">{{ $mutation->motif ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Date de Dépôt</label>
                            <p class="mb-0 fw-semibold">{{ $mutation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($mutation->mutation_type === 'externe')
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Validée par (Direction Actuelle)</label>
                                <p class="mb-0 fw-semibold">
                                    @if($mutation->approvedByCurrentDirection)
                                        {{ $mutation->approvedByCurrentDirection->fname . ' ' . $mutation->approvedByCurrentDirection->lname }}
                                        <small class="text-muted d-block">{{ $mutation->approved_by_current_direction_at->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                            @if($isFinalValidation)
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Validée par (Direction Destination)</label>
                                    <p class="mb-0 fw-semibold">
                                        @if($mutation->approvedByDestinationDirection)
                                            {{ $mutation->approvedByDestinationDirection->fname . ' ' . $mutation->approvedByDestinationDirection->lname }}
                                            <small class="text-muted d-block">{{ $mutation->approved_by_destination_direction_at->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                @if($mutation->sentToDestinationBySuperRh)
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Envoyée à la destination par</label>
                                        <p class="mb-0 fw-semibold">
                                            {{ $mutation->sentToDestinationBySuperRh->fname . ' ' . $mutation->sentToDestinationBySuperRh->lname }}
                                            <small class="text-muted d-block">{{ $mutation->sent_to_destination_by_super_rh_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">Validée par</label>
                                <p class="mb-0 fw-semibold">
                                    @if($mutation->approvedByCurrentDirection)
                                        {{ $mutation->approvedByCurrentDirection->fname . ' ' . $mutation->approvedByCurrentDirection->lname }}
                                        <small class="text-muted d-block">{{ $mutation->approved_by_current_direction_at->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($isIntermediateReview)
                <!-- Intermediate Review Form Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Décision Intermédiaire</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Cette mutation externe a été approuvée par la direction actuelle. 
                            Vous pouvez soit l'envoyer à la direction de destination pour validation, soit la rejeter maintenant.
                        </div>

                        <!-- Send to Destination Form -->
                        <form action="{{ route('mutations.super-rh.validate.store', $mutation->id) }}" method="POST" id="sendToDestinationForm" class="mb-4" data-skip-loading="true">
                            @csrf
                            <input type="hidden" name="action" value="send_to_destination">
                            
                            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer à la Destination
                                </button>
                            </div>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('mutations.super-rh.validate.store', $mutation->id) }}" method="POST" id="rejectForm" data-skip-loading="true">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            
                            <div class="mb-3">
                                <label for="rejection_reason_super_rh" class="form-label fw-semibold">
                                    <i class="fas fa-comment-alt me-2 text-danger"></i>
                                    Motif de Rejet <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('rejection_reason_super_rh') is-invalid @enderror" 
                                          id="rejection_reason_super_rh" 
                                          name="rejection_reason_super_rh" 
                                          rows="4" 
                                          placeholder="Expliquez pourquoi cette mutation ne doit pas être envoyée à la direction de destination..."
                                          required>{{ old('rejection_reason_super_rh') }}</textarea>
                                <div id="rejection_reason_error" class="invalid-feedback d-none"></div>
                                @error('rejection_reason_super_rh')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Minimum 10 caractères requis.
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times-circle me-2"></i>Rejeter la Mutation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- Final Validation Form Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-calendar-check text-warning"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Définir la Date de Début d'Affectation</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mutations.super-rh.validate.store', $mutation->id) }}" method="POST" id="validationForm" novalidate data-skip-loading="true">
                            @csrf
                            <input type="hidden" name="action" value="final_validate">
                            
                            <div class="mb-4">
                                <label for="date_debut_affectation" class="form-label fw-semibold">
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    Date de Début d'Affectation <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date_debut_affectation') is-invalid @enderror" 
                                       id="date_debut_affectation" 
                                       name="date_debut_affectation" 
                                       value="{{ old('date_debut_affectation', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                <div id="date_debut_affectation_error" class="invalid-feedback d-none"></div>
                                @error('date_debut_affectation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    La date de début d'affectation doit être aujourd'hui ou une date future. Cette date sera utilisée pour créer le nouveau parcours de l'agent.
                                </small>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary" data-skip-global-loading="true">
                                    <i class="fas fa-check-circle me-2" id="submitIcon"></i>
                                    <span id="submitText">Valider et Définir la Date</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($isFinalValidation)
    // Final validation form
    const form = document.getElementById('validationForm');
    const dateField = document.getElementById('date_debut_affectation');
    const dateErrorDiv = document.getElementById('date_debut_affectation_error');
    
    // Clear error on input
    if (dateField) {
        dateField.addEventListener('change', function() {
            this.classList.remove('is-invalid');
            dateErrorDiv.classList.add('d-none');
            dateErrorDiv.textContent = '';
            
            // Hide Laravel error messages
            const laravelError = this.parentElement.querySelector('.text-danger.small');
            if (laravelError) {
                laravelError.style.display = 'none';
            }
        });
    }
    
    // Custom validation on submit
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Hide all Laravel error messages first
            document.querySelectorAll('.text-danger.small').forEach(error => {
                error.style.display = 'none';
            });
            
            // Validate date
            dateField.classList.remove('is-invalid');
            dateErrorDiv.classList.add('d-none');
            dateErrorDiv.textContent = '';
            
            if (!dateField.value.trim()) {
                isValid = false;
                dateField.classList.add('is-invalid');
                dateErrorDiv.textContent = 'La date de début d\'affectation est requise';
                dateErrorDiv.classList.remove('d-none');
                e.preventDefault();
                return false;
            }
            
            // Validate date is today or future
            const selectedDate = new Date(dateField.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                isValid = false;
                dateField.classList.add('is-invalid');
                dateErrorDiv.textContent = 'La date de début d\'affectation doit être aujourd\'hui ou une date future';
                dateErrorDiv.classList.remove('d-none');
                e.preventDefault();
                return false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // If valid, proceed with submission
            form.setAttribute('data-submitting', 'true');
            
            const submitBtn = document.getElementById('submitBtn');
            const submitIcon = document.getElementById('submitIcon');
            
            // Disable button and show loading state
            if (submitBtn && !submitBtn.hasAttribute('data-submitting')) {
                submitBtn.setAttribute('data-submitting', 'true');
                submitBtn.disabled = true;
                
                if (submitIcon) {
                    submitIcon.className = 'fas fa-spinner fa-spin me-2';
                }
            }
        });
    }
    @else
    // Intermediate review forms
    const rejectForm = document.getElementById('rejectForm');
    const rejectionReasonField = document.getElementById('rejection_reason_super_rh');
    const rejectionReasonErrorDiv = document.getElementById('rejection_reason_error');
    
    if (rejectionReasonField) {
        rejectionReasonField.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            rejectionReasonErrorDiv.classList.add('d-none');
            rejectionReasonErrorDiv.textContent = '';
            
            const laravelError = this.parentElement.querySelector('.text-danger.small');
            if (laravelError) {
                laravelError.style.display = 'none';
            }
        });
    }
    
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            document.querySelectorAll('.text-danger.small').forEach(error => {
                error.style.display = 'none';
            });
            
            rejectionReasonField.classList.remove('is-invalid');
            rejectionReasonErrorDiv.classList.add('d-none');
            rejectionReasonErrorDiv.textContent = '';
            
            if (!rejectionReasonField.value.trim() || rejectionReasonField.value.trim().length < 10) {
                isValid = false;
                rejectionReasonField.classList.add('is-invalid');
                rejectionReasonErrorDiv.textContent = 'Le motif de rejet doit contenir au moins 10 caractères';
                rejectionReasonErrorDiv.classList.remove('d-none');
                e.preventDefault();
                return false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    }
    @endif
});
</script>
@endpush

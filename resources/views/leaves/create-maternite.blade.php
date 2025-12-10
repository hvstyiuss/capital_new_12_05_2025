@extends('layouts.app')

@section('title', 'Demande de Congé Maternité')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <p class="text-muted mb-2">Déclarer un congé maternité (98 jours)</p>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Congé Maternité:</strong> 14 semaines (98 jours) - Approuvé automatiquement, aucune validation chef requise.
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">Nouvelle Demande de Congé Maternité</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('hr.leaves.store-maternite') }}" method="POST">
                @csrf

                <!-- Dates -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="date_declaration" class="form-label">Date de Déclaration <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_declaration') is-invalid @enderror" 
                               id="date_declaration" name="date_declaration" 
                               value="{{ old('date_declaration', date('Y-m-d')) }}" required>
                        @error('date_declaration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="date_depart" class="form-label">Date de Départ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_depart') is-invalid @enderror" 
                               id="date_depart" name="date_depart" 
                               value="{{ old('date_depart') }}" required>
                        @error('date_depart')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="date_retour" class="form-label">Date de Retour</label>
                        <input type="date" class="form-control @error('date_retour') is-invalid @enderror" 
                               id="date_retour" name="date_retour" 
                               value="{{ old('date_retour') }}">
                        @error('date_retour')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Calculée automatiquement: 98 jours après la date de départ</small>
                    </div>
                </div>

                <!-- Number of Days (Fixed at 98) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nbr_jours_demandes" class="form-label">Nombre de Jours <span class="text-danger">*</span></label>
                        <input type="number" class="form-control bg-light @error('nbr_jours_demandes') is-invalid @enderror" 
                               id="nbr_jours_demandes" name="nbr_jours_demandes" 
                               value="{{ old('nbr_jours_demandes', 98) }}" min="1" max="98" required readonly>
                        <small class="text-muted">Durée fixe: 98 jours (14 semaines)</small>
                        @error('nbr_jours_demandes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Observation -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label for="observation" class="form-label">Observation</label>
                        <textarea class="form-control @error('observation') is-invalid @enderror" 
                                  id="observation" name="observation" rows="3">{{ old('observation') }}</textarea>
                        @error('observation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                    <a href="{{ route('hr.leaves.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate return date and days when departure date changes
    document.addEventListener('DOMContentLoaded', function() {
        const dateDepart = document.getElementById('date_depart');
        const dateRetour = document.getElementById('date_retour');
        const nbrJours = document.getElementById('nbr_jours_demandes');

        function calculateReturnDate() {
            if (dateDepart.value) {
                const start = new Date(dateDepart.value);
                const end = new Date(start);
                end.setDate(end.getDate() + 98); // Add 98 days
                
                const year = end.getFullYear();
                const month = String(end.getMonth() + 1).padStart(2, '0');
                const day = String(end.getDate()).padStart(2, '0');
                dateRetour.value = `${year}-${month}-${day}`;
                
                nbrJours.value = 98;
            }
        }

        dateDepart.addEventListener('change', calculateReturnDate);
    });
</script>
@endpush
@endsection


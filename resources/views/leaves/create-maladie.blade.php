@extends('layouts.app')

@section('title', 'Demande de Congé Maladie')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <p class="text-muted mb-2">Déclarer un congé maladie</p>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">Nouvelle Demande de Congé Maladie</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('hr.leaves.store-maladie') }}" method="POST">
                @csrf

                <!-- Type Maladie -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="type_maladie_id" class="form-label">Type de Maladie <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_maladie_id') is-invalid @enderror" id="type_maladie_id" name="type_maladie_id" required>
                            <option value="">Sélectionner un type</option>
                            @foreach($typeMaladies as $typeMaladie)
                                <option value="{{ $typeMaladie->id }}" {{ old('type_maladie_id') == $typeMaladie->id ? 'selected' : '' }}>
                                    {{ $typeMaladie->display_name }}
                                    @if($typeMaladie->max_duration_days)
                                        (Max: {{ $typeMaladie->max_duration_days }} jours)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('type_maladie_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

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
                        <label for="date_constatation" class="form-label">Date de Constatation</label>
                        <input type="date" class="form-control @error('date_constatation') is-invalid @enderror" 
                               id="date_constatation" name="date_constatation" 
                               value="{{ old('date_constatation') }}">
                        @error('date_constatation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
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
                    </div>
                    <div class="col-md-4">
                        <label for="nbr_jours_demandes" class="form-label">Nombre de Jours <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('nbr_jours_demandes') is-invalid @enderror" 
                               id="nbr_jours_demandes" name="nbr_jours_demandes" 
                               value="{{ old('nbr_jours_demandes') }}" min="1" required>
                        @error('nbr_jours_demandes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Reference Arret -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="reference_arret" class="form-label">Référence d'Arrêt</label>
                        <input type="text" class="form-control @error('reference_arret') is-invalid @enderror" 
                               id="reference_arret" name="reference_arret" 
                               value="{{ old('reference_arret') }}" maxlength="255">
                        @error('reference_arret')
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
    // Auto-calculate days when dates change
    document.addEventListener('DOMContentLoaded', function() {
        const dateDepart = document.getElementById('date_depart');
        const dateRetour = document.getElementById('date_retour');
        const nbrJours = document.getElementById('nbr_jours_demandes');

        function calculateDays() {
            if (dateDepart.value && dateRetour.value) {
                const start = new Date(dateDepart.value);
                const end = new Date(dateRetour.value);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both days
                if (diffDays > 0) {
                    nbrJours.value = diffDays;
                }
            }
        }

        dateDepart.addEventListener('change', calculateDays);
        dateRetour.addEventListener('change', calculateDays);
    });
</script>
@endpush
@endsection


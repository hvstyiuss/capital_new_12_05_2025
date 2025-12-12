@extends('layouts.app')

@section('title', 'Échange de Chefs')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-sync-alt me-2 text-primary"></i>
                    Échange de Chefs
                </h1>
                <p class="text-muted mb-0">Échangez les chefs entre deux entités</p>
            </div>
            <div>
                <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Swap Chefs Form -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-exchange-alt me-2 text-primary"></i>Formulaire d'Échange
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                Cette fonctionnalité permet d'échanger simultanément deux chefs entre leurs entités respectives. 
                Les parcours professionnels seront mis à jour automatiquement.
            </p>

            <form method="POST" action="{{ route('hr.users.swap-chefs.store') }}" id="swapChefsForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="entity1_id" class="form-label fw-semibold">
                            Première Entité <span class="text-danger">*</span>
                        </label>
                        <select id="entity1_id" name="entity1_id" class="form-select @error('entity1_id') is-invalid @enderror" required>
                            <option value="">Sélectionnez une entité avec chef</option>
                            @foreach($entitesWithChefs as $entite)
                                <option value="{{ $entite->id }}" {{ old('entity1_id') == $entite->id ? 'selected' : '' }}>
                                    {{ $entite->name }} (Chef: {{ optional($entite->chef)->name ?? $entite->chef_ppr }})
                                </option>
                            @endforeach
                        </select>
                        @error('entity1_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Sélectionnez la première entité
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="entity2_id" class="form-label fw-semibold">
                            Deuxième Entité <span class="text-danger">*</span>
                        </label>
                        <select id="entity2_id" name="entity2_id" class="form-select @error('entity2_id') is-invalid @enderror" required>
                            <option value="">Sélectionnez une entité avec chef</option>
                            @foreach($entitesWithChefs as $entite)
                                <option value="{{ $entite->id }}" {{ old('entity2_id') == $entite->id ? 'selected' : '' }}>
                                    {{ $entite->name }} (Chef: {{ optional($entite->chef)->name ?? $entite->chef_ppr }})
                                </option>
                            @endforeach
                        </select>
                        @error('entity2_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Sélectionnez la deuxième entité
                        </small>
                    </div>

                    <div class="col-md-12">
                        <label for="date" class="form-label fw-semibold">
                            Date d'échange <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               id="date" 
                               name="date" 
                               class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date', date('Y-m-d')) }}" 
                               min="{{ date('Y-m-d') }}" 
                               required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>La date doit être aujourd'hui ou une date future
                        </small>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="swapChefsBtn">
                        <i class="fas fa-sync-alt me-2"></i>Échanger les Chefs
                    </button>
                    <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Information Card -->
    @if($entitesWithChefs->isEmpty())
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune entité avec chef disponible</h5>
                <p class="text-muted">Il n'y a actuellement aucune entité avec un chef assigné pour effectuer un échange.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('swapChefsForm');
    const swapChefsBtn = document.getElementById('swapChefsBtn');
    const entity1Select = document.getElementById('entity1_id');
    const entity2Select = document.getElementById('entity2_id');

    // Prevent selecting the same entity in both dropdowns
    function validateEntitySelection() {
        if (entity1Select.value && entity2Select.value && entity1Select.value === entity2Select.value) {
            entity2Select.setCustomValidity('Veuillez sélectionner une entité différente de la première.');
        } else {
            entity2Select.setCustomValidity('');
        }
    }

    entity1Select.addEventListener('change', validateEntitySelection);
    entity2Select.addEventListener('change', validateEntitySelection);

    // Form submission with confirmation
    form.addEventListener('submit', function(e) {
        const entity1Id = entity1Select.value;
        const entity2Id = entity2Select.value;
        const date = document.getElementById('date').value;

        if (!entity1Id || !entity2Id) {
            e.preventDefault();
            alert('Veuillez sélectionner deux entités avec des chefs.');
            return;
        }

        if (entity1Id === entity2Id) {
            e.preventDefault();
            alert('Veuillez sélectionner deux entités différentes.');
            return;
        }

        if (!date) {
            e.preventDefault();
            alert('Veuillez sélectionner une date d\'échange.');
            return;
        }

        const entity1Option = entity1Select.options[entity1Select.selectedIndex];
        const entity2Option = entity2Select.options[entity2Select.selectedIndex];
        const entity1Name = entity1Option ? entity1Option.text.split(' (Chef:')[0] : '';
        const entity2Name = entity2Option ? entity2Option.text.split(' (Chef:')[0] : '';

        if (!confirm(`Êtes-vous sûr de vouloir échanger les chefs entre ces deux entités?\n\nEntité 1: ${entity1Name}\nEntité 2: ${entity2Name}\n\nDate: ${date}`)) {
            e.preventDefault();
            return;
        }

        // Disable button during submission
        if (swapChefsBtn) {
            swapChefsBtn.disabled = true;
            swapChefsBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Échange en cours...';
        }
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

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-select, .form-control {
    border-radius: 0.5rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    padding: 0.5rem 1.5rem;
}

.alert {
    border-radius: 0.5rem;
    border: none;
}
</style>
@endpush




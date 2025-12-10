@extends('layouts.app')

@section('title', 'Créer un Rôle')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gradient-primary rounded-3 p-3 shadow-sm">
                <i class="fas fa-shield-alt text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Créer un Rôle</h1>
                <p class="text-muted mb-0">Ajoutez un nouveau rôle au système</p>
            </div>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Create Form Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-shield-alt text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Formulaire de création</h5>
                            <small class="text-muted">Remplissez les informations pour créer un nouveau rôle</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('roles.store') }}" method="POST" id="roleCreateForm" novalidate autocomplete="off">
                        @csrf

                        <!-- Role Info -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                Informations du Rôle
                            </h6>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    Nom du rôle <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Ex: manager, admin, user">
                                <div id="name_error" class="invalid-feedback d-none"></div>
                                @error('name')
                                    <div class="text-danger small mt-1 validation-error">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Le nom du rôle doit être unique dans le système
                                </small>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" id="submitBtn" class="btn btn-primary" data-skip-global-loading="true">
                                <i class="fas fa-save me-2" id="submitIcon"></i>
                                <span id="submitText">Créer le Rôle</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-lightbulb text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Conseils</h6>
                            <small class="text-muted">
                                Bonnes pratiques pour créer un rôle
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="mb-0 text-muted small ps-3">
                        <li class="mb-2">Utilisez des noms descriptifs et clairs</li>
                        <li class="mb-2">Évitez les noms trop génériques ou ambigus</li>
                        <li class="mb-2">Respectez la convention de nommage existante</li>
                        <li class="mb-0">Vous pourrez ajouter des permissions après la création</li>
                    </ul>
                </div>
            </div>

            <!-- Existing Roles Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-list text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Rôles existants</h6>
                            <small class="text-muted">
                                Consultez les rôles déjà créés
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted small mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Vérifiez que le nom que vous souhaitez utiliser n'existe pas déjà.
                    </p>
                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-eye me-2"></i>Voir tous les rôles
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('roleCreateForm');
    const nameField = document.getElementById('name');
    const nameErrorDiv = document.getElementById('name_error');

    if (!form || !nameField) {
        return;
    }

    // Clear error on input
    nameField.addEventListener('input', function () {
        this.classList.remove('is-invalid');
        if (nameErrorDiv) {
            nameErrorDiv.classList.add('d-none');
            nameErrorDiv.textContent = '';
        }

        const laravelError = this.parentElement.querySelector('.validation-error');
        if (laravelError) {
            laravelError.style.display = 'none';
        }
    });

    // Custom validation on submit
    form.addEventListener('submit', function (e) {
        let isValid = true;

        // Hide Laravel errors
        document.querySelectorAll('.validation-error').forEach(err => {
            err.style.display = 'none';
        });

        if (nameField && nameErrorDiv) {
            nameField.classList.remove('is-invalid');
            nameErrorDiv.classList.add('d-none');
            nameErrorDiv.textContent = '';

            if (!nameField.value.trim()) {
                isValid = false;
                nameField.classList.add('is-invalid');
                nameErrorDiv.textContent = 'Le nom du rôle est requis';
                nameErrorDiv.classList.remove('d-none');
                e.preventDefault();
                return false;
            }
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        // Loading state
        const submitBtn = document.getElementById('submitBtn');
        const submitIcon = document.getElementById('submitIcon');
        if (submitBtn && !submitBtn.hasAttribute('data-submitting')) {
            submitBtn.setAttribute('data-submitting', 'true');
            submitBtn.disabled = true;
            if (submitIcon) {
                submitIcon.className = 'fas fa-spinner fa-spin me-2';
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.validation-error {
    display: block !important;
}

.validation-error:not(:first-of-type) {
    display: none !important;
}

/* Hide browser's native validation messages */
input:invalid,
select:invalid,
textarea:invalid {
    box-shadow: none;
}

input:invalid:not(:focus):not(:placeholder-shown),
select:invalid:not(:focus):not(:placeholder-shown),
textarea:invalid:not(:focus):not(:placeholder-shown) {
    border-color: #dc3545;
}

/* Prevent multiple error messages from showing */
.form-group .validation-error ~ .validation-error {
    display: none !important;
}
</style>
@endpush

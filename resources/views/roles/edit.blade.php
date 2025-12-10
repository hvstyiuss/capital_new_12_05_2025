@extends('layouts.app')

@section('title', 'Modifier le Rôle')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gradient-primary rounded-3 p-3 shadow-sm">
                <i class="fas fa-user-shield text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Modifier le Rôle</h1>
                <p class="text-muted mb-0">
                    Modifiez les informations du rôle
                    <span class="fw-semibold">{{ $role->name }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Edit Form Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-edit text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Formulaire de modification</h5>
                            <small class="text-muted">Mettez à jour les informations du rôle</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('roles.update', $role) }}" method="POST" id="roleEditForm" novalidate autocomplete="off">
                        @csrf
                        @method('PUT')

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
                                       value="{{ old('name', $role->name) }}"
                                       placeholder="Ex: admin, manager, super collaborateur rh">
                                <div id="name_error" class="invalid-feedback d-none"></div>
                                @error('name')
                                    <div class="text-danger small mt-1 validation-error">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Utilisez un nom clair et cohérent (ex: <code>admin</code>, <code>manager</code>, <code>super collaborateur rh</code>).
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
                                <span id="submitText">Mettre à Jour</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Permissions Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-key text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Permissions associées</h6>
                            <small class="text-muted">
                                Aperçu des permissions liées à ce rôle
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($role->permissions->count() > 0)
                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $role->permissions->count() }} permission{{ $role->permissions->count() > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="list-group small" style="max-height: 260px; overflow-y: auto;">
                            @foreach($role->permissions as $permission)
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 border-bottom py-2 px-0">
                                    <div class="me-2">
                                        <div class="fw-semibold text-truncate" title="{{ $permission->name }}">
                                            {{ $permission->name }}
                                        </div>
                                    </div>
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border-dashed mb-0 small">
                            <i class="fas fa-info-circle me-2 text-muted"></i>
                            Ce rôle n'a actuellement aucune permission associée.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-lightbulb text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2">Conseils pour modifier un rôle</h6>
                            <ul class="mb-0 text-muted small ps-3">
                                <li>Utilisez un nom descriptif et cohérent avec vos autres rôles.</li>
                                <li>Évitez de renommer un rôle utilisé dans des règles métier sensibles.</li>
                                <li>Adaptez ensuite les permissions associées au rôle si nécessaire.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('roleEditForm');
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


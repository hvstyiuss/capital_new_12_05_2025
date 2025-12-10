@extends('layouts.app')

@section('title', 'Modifier la Permission')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gradient-primary rounded-3 p-3 shadow-sm">
                <i class="fas fa-key text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Modifier la Permission</h1>
                <p class="text-muted mb-0">
                    Modifiez les informations de la permission
                    <span class="fw-semibold">{{ $permission->name }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Edit Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-edit text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Formulaire de modification</h5>
                            <small class="text-muted">Mettez à jour les informations de la permission</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('permissions.update', $permission) }}" method="POST" id="permissionEditForm" novalidate autocomplete="off">
                        @csrf
                        @method('PUT')

                        <!-- Permission Name (selection depuis un catalogue, pas de texte libre) -->
                        <div class="mb-4">
                            <label for="permission_search" class="form-label fw-semibold">
                                <i class="fas fa-tag me-2 text-primary"></i>
                                Nom de la permission <span class="text-danger">*</span>
                            </label>

                            @php
                                $catalog = $permissionCatalog ?? collect();
                            @endphp

                            @if($catalog->isNotEmpty())
                                <!-- Champ masqué effectivement soumis -->
                                <input type="hidden"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $permission->name) }}">

                                <!-- Champ de recherche -->
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           id="permission_search"
                                           class="form-control"
                                           placeholder="Rechercher une permission (ex: utilisateurs, mutations, congés...)"
                                           autocomplete="off">
                                </div>

                                <!-- Liste des permissions filtrables -->
                                <div class="border rounded p-2 bg-light" style="max-height: 260px; overflow-y: auto;">
                                    <ul class="list-unstyled mb-0" id="permission_list">
                                        @foreach($catalog as $item)
                                            <li class="permission-item px-2 py-1 mb-1 rounded cursor-pointer"
                                                data-name="{{ $item['name'] }}"
                                                data-label="{{ $item['label'] }}"
                                                data-group="{{ $item['group'] }}"
                                                data-search="{{ strtolower($item['name'].' '.$item['label'].' '.$item['group']) }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-secondary me-2">{{ $item['group'] }}</span>
                                                        <strong>{{ $item['label'] }}</strong>
                                                    </div>
                                                    <code class="small text-muted">{{ $item['name'] }}</code>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div id="no_permission_result" class="text-muted small text-center py-2 d-none">
                                        Aucune permission ne correspond à votre recherche.
                                    </div>
                                </div>

                                <div id="name_error" class="invalid-feedback d-none"></div>
                                @error('name')
                                    <div class="text-danger small mt-1 validation-error">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Utilisez des noms clairs, en notation par points (ex: <code>users.view</code>, <code>mutations.create</code>).
                                </small>
                            @else
                                <!-- Fallback : si pas de catalogue, texte libre -->
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $permission->name) }}"
                                       placeholder="Ex: users.view, mutations.approve">
                                <div id="name_error" class="invalid-feedback d-none"></div>
                                @error('name')
                                    <div class="text-danger small mt-1 validation-error">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Utilisez des noms clairs, en notation par points (ex: <code>users.view</code>, <code>mutations.create</code>).
                                </small>
                            @endif
                        </div>

                        <!-- Roles Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>
                                Rôles associés
                            </label>
                            @if($roles->count() > 0)
                                @php
                                    $selectedRoleIds = old('roles', $permission->roles->pluck('id')->toArray());
                                @endphp
                                <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                    <div class="row g-2">
                                        @foreach($roles as $role)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="roles[]"
                                                           value="{{ $role->id }}"
                                                           id="role_{{ $role->id }}"
                                                           {{ in_array($role->id, $selectedRoleIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Cochez un ou plusieurs rôles qui doivent posséder cette permission.
                                </small>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucun rôle disponible. Veuillez créer des rôles d'abord.
                                </div>
                            @endif
                            @error('roles')
                                <div class="text-danger small mt-1 validation-error">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            @error('roles.*')
                                <div class="text-danger small mt-1 validation-error">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
                            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
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
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('permissionEditForm');
    const nameField = document.getElementById('name');
    const nameErrorDiv = document.getElementById('name_error');

    const permissionSearch = document.getElementById('permission_search');
    const permissionList = document.getElementById('permission_list');
    const noPermissionResult = document.getElementById('no_permission_result');

    if (!form || !nameField) {
        return;
    }

    // Gestion du catalogue (si présent)
    if (permissionList && permissionSearch) {
        const items = Array.from(permissionList.querySelectorAll('.permission-item'));

        // Mettre en surbrillance l'élément correspondant à la valeur actuelle
        const initialName = nameField.value || '';
        if (initialName) {
            items.forEach(item => {
                if (item.dataset.name === initialName) {
                    item.classList.add('bg-primary', 'bg-opacity-10');
                }
            });
        }

        // Clic sur un élément : sélection de la permission
        items.forEach(item => {
            item.addEventListener('click', function () {
                const value = this.dataset.name || '';

                if (nameField) {
                    nameField.value = value;
                    nameField.dispatchEvent(new Event('input'));
                }

                // Surbrillance
                items.forEach(i => i.classList.remove('bg-primary', 'bg-opacity-10'));
                this.classList.add('bg-primary', 'bg-opacity-10');
            });
        });

        // Filtrage dynamique via la barre de recherche
        const filterList = () => {
            const term = (permissionSearch.value || '').toLowerCase().trim();
            let visibleCount = 0;

            items.forEach(item => {
                const haystack = item.dataset.search || '';
                if (!term || haystack.includes(term)) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            if (noPermissionResult) {
                if (visibleCount === 0) {
                    noPermissionResult.classList.remove('d-none');
                } else {
                    noPermissionResult.classList.add('d-none');
                }
            }
        };

        permissionSearch.addEventListener('input', filterList);
        permissionSearch.addEventListener('paste', () => setTimeout(filterList, 10));
        permissionSearch.addEventListener('keyup', (e) => {
            if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes(e.key)) {
                filterList();
            }
        });

        // Initial: afficher tout
        filterList();
    }

    // Clear error on input (pour le champ caché ou texte libre)
    if (nameField) {
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
    }

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
                nameErrorDiv.textContent = 'Le nom de la permission est requis';
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


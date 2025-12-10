@extends('layouts.app')

@section('title', 'Modifier l\'Utilisateur')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-gray-800">
                    <i class="fas fa-user-edit me-2 text-primary"></i>
                    Modifier l'Utilisateur
                </h1>
                <p class="text-muted mb-0">Modifier les informations de {{ $user->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('hr.users.show', $user) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>Voir
                </a>
                <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        Modifier l'Utilisateur
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('hr.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3 fw-semibold">
                                    <i class="fas fa-id-card me-2"></i>Informations Personnelles
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="fname" class="form-label fw-semibold">
                                        Prénom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('fname') is-invalid @enderror" 
                                           id="fname" 
                                           name="fname" 
                                           value="{{ old('fname', $user->fname) }}" 
                                           required>
                                    @error('fname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="lname" class="form-label fw-semibold">Nom</label>
                                    <input type="text" 
                                           class="form-control @error('lname') is-invalid @enderror" 
                                           id="lname" 
                                           name="lname" 
                                           value="{{ old('lname', $user->lname) }}">
                                    @error('lname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email ?? $user->userInfo->email ?? '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="ppr" class="form-label fw-semibold">
                                        PPR <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('ppr') is-invalid @enderror bg-light" 
                                           id="ppr" 
                                           name="ppr" 
                                           value="{{ old('ppr', $user->ppr) }}" 
                                           required 
                                           readonly>
                                    @error('ppr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Le PPR ne peut pas être modifié
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label for="cin" class="form-label fw-semibold">CIN</label>
                                    <input type="text" 
                                           class="form-control @error('cin') is-invalid @enderror" 
                                           id="cin" 
                                           name="cin" 
                                           value="{{ old('cin', $user->userInfo->cin ?? '') }}">
                                    @error('cin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="gsm" class="form-label fw-semibold">GSM</label>
                                    <input type="text" 
                                           class="form-control @error('gsm') is-invalid @enderror" 
                                           id="gsm" 
                                           name="gsm" 
                                           value="{{ old('gsm', $user->userInfo->gsm ?? '') }}">
                                    @error('gsm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="adresse" class="form-label fw-semibold">Adresse</label>
                                    <input type="text" 
                                           class="form-control @error('adresse') is-invalid @enderror" 
                                           id="adresse" 
                                           name="adresse" 
                                           value="{{ old('adresse', $user->userInfo->adresse ?? '') }}">
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="rib" class="form-label fw-semibold">RIB</label>
                                    <input type="text" 
                                           class="form-control @error('rib') is-invalid @enderror" 
                                           id="rib" 
                                           name="rib" 
                                           value="{{ old('rib', $user->userInfo->rib ?? '') }}">
                                    @error('rib')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3 fw-semibold">
                                    <i class="fas fa-briefcase me-2"></i>Informations Professionnelles
                                </h6>

                                <div class="mb-3">
                                    <label for="echelle_id" class="form-label fw-semibold">Échelle</label>
                                    <select class="form-select @error('echelle_id') is-invalid @enderror" 
                                            id="echelle_id" 
                                            name="echelle_id">
                                        <option value="">Sélectionner une échelle</option>
                                        @foreach($echelles as $echelle)
                                            <option value="{{ $echelle->id }}" 
                                                {{ old('echelle_id', $user->userInfo->echelle_id ?? '') == $echelle->id ? 'selected' : '' }}>
                                                {{ $echelle->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('echelle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="grade_id" class="form-label fw-semibold">Grade</label>
                                    <select class="form-select @error('grade_id') is-invalid @enderror" 
                                            id="grade_id" 
                                            name="grade_id">
                                        <option value="">Sélectionner un grade</option>
                                        @foreach($grades as $grade)
                                            <option value="{{ $grade->id }}" 
                                                data-echelle-id="{{ $grade->echelle_id }}"
                                                {{ old('grade_id', $user->userInfo->grade_id ?? '') == $grade->id ? 'selected' : '' }}>
                                                {{ $grade->name }} 
                                                @if($grade->echelle)
                                                    ({{ $grade->echelle->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('grade_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="corps" class="form-label fw-semibold">Corps fonctionnel</label>
                                    <select class="form-select @error('corps') is-invalid @enderror" 
                                            id="corps" 
                                            name="corps">
                                        <option value="">Sélectionner un corps fonctionnel</option>
                                        <option value="forestier" {{ old('corps', $user->userInfo->corps ?? '') == 'forestier' ? 'selected' : '' }}>Forestier</option>
                                        <option value="support" {{ old('corps', $user->userInfo->corps ?? '') == 'support' ? 'selected' : '' }}>Support</option>
                                    </select>
                                    @error('corps')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="responsable" 
                                               name="responsable" 
                                               value="1"
                                               {{ old('responsable', $user->userInfo->responsable ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="responsable">
                                            Responsable
                                        </label>
                                    </div>
                                    @error('responsable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_active">
                                            Compte actif
                                        </label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <h6 class="text-primary mb-3 fw-semibold">
                                    <i class="fas fa-shield-alt me-2"></i>Sécurité et Rôles
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Nouveau mot de passe</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Laisser vide pour ne pas changer">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword()"
                                                title="Afficher/Masquer">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Laissez vide pour conserver le mot de passe actuel
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">
                                        Confirmer le nouveau mot de passe
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirmez le nouveau mot de passe">
                                </div>

                                <div class="mb-3">
                                    <label for="roles" class="form-label fw-semibold">Rôles</label>
                                    <select class="form-select @error('roles') is-invalid @enderror" 
                                            id="roles" 
                                            name="roles[]" 
                                            multiple
                                            size="5">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" 
                                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs rôles
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="text-primary mb-3 fw-semibold">
                                    <i class="fas fa-image me-2"></i>Photo de Profil
                                </h6>
                                
                                <!-- Current Image -->
                                @if($user->image)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Image actuelle</label>
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <img src="{{ asset('storage/' . $user->image) }}" 
                                             alt="{{ $user->name }}" 
                                             class="img-thumbnail me-3" 
                                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                        <div>
                                            <p class="mb-1 fw-semibold">Image actuelle</p>
                                            <small class="text-muted">{{ $user->image }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-semibold">Nouvelle image de profil</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Formats acceptés: JPG, PNG, GIF. Taille max: 2MB
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <div id="imagePreview" class="d-none mt-3">
                                        <label class="form-label fw-semibold">Aperçu</label>
                                        <div class="p-3 bg-light rounded">
                                            <img id="previewImg" 
                                                 src="" 
                                                 alt="Aperçu" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Mettre à jour
                                    </button>
                                </div>
                            </div>
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
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

// Filter grades based on selected echelle
function filterGrades() {
    const echelleSelect = document.getElementById('echelle_id');
    const selectedEchelleId = echelleSelect.value;
    const gradeSelect = document.getElementById('grade_id');
    const allGrades = Array.from(gradeSelect.options);
    const currentGradeId = gradeSelect.value;
    
    // Show/hide grades based on echelle
    allGrades.forEach(option => {
        if (option.value === '') {
            option.style.display = '';
        } else {
            const gradeEchelleId = option.getAttribute('data-echelle-id');
            if (selectedEchelleId === '' || gradeEchelleId === selectedEchelleId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
                // Clear selection if current grade doesn't match selected echelle
                if (option.value === currentGradeId && gradeEchelleId !== selectedEchelleId) {
                    gradeSelect.value = '';
                }
            }
        }
    });
}

// Initialize filter on page load
document.addEventListener('DOMContentLoaded', function() {
    filterGrades();
});

// Update filter when echelle changes
document.getElementById('echelle_id').addEventListener('change', filterGrades);

// Image preview
const imageInput = document.getElementById('image');
if (imageInput) {
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
        }
    });
}

// Password confirmation validation
const passwordConfirmation = document.getElementById('password_confirmation');
if (passwordConfirmation) {
    passwordConfirmation.addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        
        if (password && confirmation && password !== confirmation) {
            this.setCustomValidity('Les mots de passe ne correspondent pas');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    
    if (password && confirmation && password !== confirmation) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return false;
    }
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

.card-header {
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.input-group .btn-outline-secondary {
    border-left: none;
}

.input-group .btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
    font-weight: 500;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.form-select[size] {
    min-height: 120px;
}

.img-thumbnail {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.img-thumbnail:hover {
    border-color: #0d6efd;
    transform: scale(1.05);
}

/* Remove gray hover effect on file input */
.form-control[type="file"]:hover,
.form-control[type="file"]:focus,
.form-control[type="file"]:active {
    background-color: #fff !important;
    border-color: #ced4da !important;
    color: #212529 !important;
}

.form-control[type="file"]:hover::file-selector-button,
.form-control[type="file"]:focus::file-selector-button,
.form-control[type="file"]:active::file-selector-button {
    background-color: #e9ecef !important;
    border-color: #ced4da !important;
    color: #212529 !important;
}

/* Ensure file input doesn't change color on hover */
input[type="file"]:hover {
    background-color: #fff !important;
    color: #212529 !important;
}

input[type="file"]:hover::file-selector-button {
    background-color: #e9ecef !important;
    color: #212529 !important;
}
</style>
@endpush

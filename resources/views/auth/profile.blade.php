@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <p class="text-muted mb-2">Gérez vos informations personnelles et paramètres de compte</p>
    </div>

    <!-- Profile Summary Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        @if($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle me-3" 
                                 width="60"
                                 height="60"
                                 style="object-fit: cover;">
                        @elseif($user->userInfo?->photo)
                            <img src="{{ $user->userInfo->photo_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle me-3" 
                                 width="60"
                                 height="60"
                                 style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" 
                                 style="width: 60px; height: 60px;"
                                 aria-label="Initiale de {{ $user->name }}">
                                <span class="text-primary fw-bold" style="font-size: 1.5rem;">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
                            <p class="text-muted mb-1 small">
                                PPR: {{ $user->ppr }}
                            </p>
                            <p class="text-muted mb-0 small">
                                Membre depuis {{ $user->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-success">
                        <i class="fas fa-shield-alt me-1"></i>Compte Sécurisé
                    </div>
                    <p class="text-muted small mb-0 mt-2">Votre compte est protégé et sécurisé</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Edit Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">Modifier mon profil</h5>
            <p class="text-muted small mb-0">Mettez à jour vos informations personnelles</p>
        </div>
        <div class="card-body">
            <form action="{{ route('auth.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Personal Information -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Informations Personnelles</h6>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="ppr" class="form-label">PPR <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   id="ppr" 
                                   name="ppr" 
                                   value="{{ $user->ppr }}" 
                                   readonly
                                   disabled>
                            <small class="text-muted">Le PPR ne peut pas être modifié</small>
                        </div>
                    </div>
                    
                    <!-- Profile Image Upload -->
                    <div class="mb-3">
                        <label class="form-label">Photo de profil</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" 
                                         alt="Photo actuelle" 
                                         id="current_image_preview"
                                         class="rounded-circle" 
                                         width="60"
                                         height="60"
                                         style="object-fit: cover;">
                                @elseif($user->userInfo?->photo)
                                    <img src="{{ $user->userInfo->photo_url }}" 
                                         alt="Photo actuelle" 
                                         id="current_image_preview"
                                         class="rounded-circle" 
                                         width="60"
                                         height="60"
                                         style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" 
                                         id="current_image_preview"
                                         style="width: 60px; height: 60px;"
                                         aria-label="Initiale de {{ $user->name }}">
                                        <span class="text-primary fw-bold" style="font-size: 1.5rem;">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <img src="" 
                                     alt="Aperçu" 
                                     id="image_preview" 
                                     class="rounded-circle d-none" 
                                     width="60"
                                     height="60"
                                     style="object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" 
                                       class="form-control @error('image') is-invalid @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/jpeg,image/png,image/jpg"
                                       onchange="previewImage(this)"
                                       aria-describedby="image-help image-error">
                                <small id="image-help" class="text-muted">Formats acceptés: JPG, JPEG, PNG. Taille max: 7 Mo</small>
                                @error('image')
                                    <div id="image-error" class="invalid-feedback d-block" role="alert">
                                        <i class="fas fa-exclamation-circle me-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Password Change Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-lock me-2"></i>Changer le mot de passe
                    </h6>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Entrez votre mot de passe actuel"
                                       autocomplete="current-password">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        onclick="togglePasswordVisibility('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       placeholder="Entrez votre nouveau mot de passe"
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        onclick="togglePasswordVisibility('new_password')">
                                    <i class="fas fa-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 8 caractères</small>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       placeholder="Confirmez votre nouveau mot de passe"
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        onclick="togglePasswordVisibility('new_password_confirmation')">
                                    <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note importante:</strong> Pour changer votre mot de passe, vous devez fournir votre mot de passe actuel. Laissez les champs de mot de passe vides si vous ne souhaitez pas le modifier.
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour le profil
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Information -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">Informations du Compte</h5>
            <p class="text-muted small mb-0">Détails et statistiques de votre compte</p>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Compte créé le</h6>
                                    <p class="text-muted small mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Dernière connexion</h6>
                                    <p class="text-muted small mb-0">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-shield-alt text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Statut</h6>
                                    <p class="text-muted small mb-0">Compte actif et sécurisé</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (!field || !icon) return;
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const currentPreview = document.getElementById('current_image_preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (currentPreview) {
                currentPreview.classList.add('d-none');
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('d-none');
        if (currentPreview) {
            currentPreview.classList.remove('d-none');
        }
    }
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Content -->
    <div class="mb-8">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/20 p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Créer un Utilisateur</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-lg mt-2">Ajouter un nouvel utilisateur au système</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('hr.users.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <!-- User Creation Form -->
            <x-card 
                title="Informations de l'Utilisateur" 
                subtitle="Remplissez les informations pour créer un nouvel utilisateur"
                variant="gradient"
                color="green"
                icon="fas fa-user"
                padding="normal"
            >
                <form action="{{ route('hr.users.store') }}" method="POST" enctype="multipart/form-data" id="userCreateForm" novalidate autocomplete="off" data-no-browser-validation>
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Personal Information -->
                        <div>
                            <h6 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <i class="fas fa-id-card text-green-500"></i>
                                Informations Personnelles
                            </h6>
                                
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user text-green-500 mr-2"></i>
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    placeholder="Ex: Jean Dupont" 
                                    autocomplete="name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('name') border-red-500 @enderror"
                                >
                                @error('name')
                                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1 validation-error">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>Nom complet de l'utilisateur
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-green-500 mr-2"></i>
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') && filter_var(old('email'), FILTER_VALIDATE_EMAIL) ? old('email') : '' }}" 
                                    placeholder="Ex: jean.dupont@example.com" 
                                    autocomplete="off"
                                    inputmode="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('email') border-red-500 @enderror"
                                >
                                @error('email')
                                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1 validation-error email-error" data-error-type="email">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>Adresse email valide requise
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="ppr" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-id-card text-green-500 mr-2"></i>
                                    PPR <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="ppr" 
                                    name="ppr" 
                                    value="{{ old('ppr') }}" 
                                    placeholder="Ex: 12345678" 
                                    pattern="[0-9]{8}" 
                                    title="Le PPR doit contenir exactement 8 chiffres"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('ppr') border-red-500 @enderror"
                                >
                                @error('ppr')
                                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1 validation-error">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>Numéro de personnel (8 chiffres)
                                </div>
                            </div>
                        </div>

                        <!-- Security & Roles -->
                        <div>
                            <h6 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <i class="fas fa-shield-alt text-green-500"></i>
                                Sécurité et Rôles
                            </h6>
                                
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock text-green-500 mr-2"></i>
                                    Mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    minlength="8"
                                    placeholder="Minimum 8 caractères"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('password') border-red-500 @enderror"
                                >
                                @error('password')
                                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1 validation-error">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-shield-alt"></i>Minimum 8 caractères avec lettres et chiffres
                                </div>
                                <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                                    <div class="w-full bg-gray-200 rounded-full h-1">
                                        <div class="bg-green-500 h-1 rounded-full transition-all duration-300" id="strengthBar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-gray-500 mt-1 block" id="strengthText">Force du mot de passe</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock text-green-500 mr-2"></i>
                                    Confirmer le mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Répétez le mot de passe"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                >
                                <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>Doit correspondre au mot de passe
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="roles" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-tag text-green-500 mr-2"></i>
                                    Rôles
                                </label>
                                <select 
                                    id="roles" 
                                    name="roles[]" 
                                    multiple
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('roles') border-red-500 @enderror"
                                >
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                            {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('roles')
                                    <div class="text-red-500 text-sm mt-1 flex items-center gap-1 validation-error">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs rôles
                                </div>
                            </div>
                            </div>
                        </div>

                    <!-- Profile Image -->
                    <div class="mt-8">
                        <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-image text-green-500"></i>
                            Photo de Profil
                        </h6>
                        
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-upload text-green-500 mr-2"></i>
                                Image de profil
                            </label>
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('image') border-red-500 @enderror"
                            >
                            @error('image')
                                <div class="text-red-500 text-sm mt-1 flex items-center gap-1 validation-error">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>Formats acceptés: JPG, PNG, GIF. Taille max: 2MB
                            </div>
                        </div>

                        <div class="mb-4">
                            <div id="imagePreview" class="hidden">
                                <img id="previewImg" src="" alt="Aperçu" 
                                     class="rounded-lg border border-gray-300" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div>
                                <a href="{{ route('hr.users.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                            <div class="flex gap-3">
                                <button 
                                    type="submit" 
                                    id="submitBtn"
                                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2"
                                >
                                    <i class="fas fa-save"></i>
                                    <span class="btn-text">Créer l'Utilisateur</span>
                                    <div class="loading-spinner hidden" style="display: none;">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                            <span class="ml-2 text-sm">Envoi en cours...</span>
                                        </div>
                                    </div>
                                </button>
                                <button 
                                    type="submit" 
                                    id="submitAndNextBtn" 
                                    name="action" 
                                    value="create_and_next"
                                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2"
                                >
                                    <i class="fas fa-plus"></i>
                                    <span class="btn-text">Créer et Ajouter un Autre</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Sidebar with Help and Information -->
        <div class="lg:col-span-1">
            <!-- Help Card -->
            <x-card 
                title="Aide" 
                subtitle="Informations utiles pour créer un utilisateur"
                variant="colored"
                color="blue"
                icon="fas fa-question-circle"
                padding="normal"
            >
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h6 class="text-blue-800 font-semibold mb-2 flex items-center gap-2">
                            <i class="fas fa-asterisk text-blue-600"></i>
                            Champs obligatoires
                        </h6>
                        <p class="text-blue-700 text-sm">Les champs marqués d'un <span class="text-red-500 font-semibold">*</span> sont obligatoires.</p>
                    </div>
                    
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <h6 class="text-green-800 font-semibold mb-2 flex items-center gap-2">
                            <i class="fas fa-lock text-green-600"></i>
                            Mot de passe
                        </h6>
                        <p class="text-green-700 text-sm">Le mot de passe doit contenir au moins 8 caractères avec lettres et chiffres.</p>
                    </div>
                    
                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <h6 class="text-purple-800 font-semibold mb-2 flex items-center gap-2">
                            <i class="fas fa-user-tag text-purple-600"></i>
                            Rôles
                        </h6>
                        <p class="text-purple-700 text-sm">Sélectionnez un ou plusieurs rôles pour définir les permissions de l'utilisateur.</p>
                    </div>
                    
                    <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <h6 class="text-orange-800 font-semibold mb-2 flex items-center gap-2">
                            <i class="fas fa-image text-orange-600"></i>
                            Image de profil
                        </h6>
                        <p class="text-orange-700 text-sm">L'image sera automatiquement redimensionnée et optimisée.</p>
                    </div>
                </div>
            </x-card>

            <!-- Available Roles Card -->
            <x-card 
                title="Rôles Disponibles" 
                subtitle="Liste des rôles que vous pouvez attribuer"
                variant="colored"
                color="green"
                icon="fas fa-shield-alt"
                padding="normal"
            >
                <div class="space-y-3">
                    @foreach($roles as $role)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                            <span class="px-3 py-1 bg-green-600 text-white text-sm font-medium rounded-full">{{ $role->name }}</span>
                            <span class="text-green-700 text-sm">{{ $role->permissions->count() }} permissions</span>
                        </div>
                    @endforeach
                </div>
            </x-card>
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

// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
    }
});

// Password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (password !== confirmation) {
        this.setCustomValidity('Les mots de passe ne correspondent pas');
    } else {
        this.setCustomValidity('');
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    
    if (password !== confirmation) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return false;
    }
});
</script>
@endpush

@push('styles')
<style>
/* Prevent duplicate validation error messages */
.validation-error {
    display: block !important;
    margin-top: 0.25rem;
}

.validation-error:not(:first-of-type) {
    display: none !important;
}

/* Hide browser's native validation messages */
input:invalid,
select:invalid,
textarea:invalid {
    box-shadow: none !important;
}

input:invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

/* Prevent multiple error messages from showing */
.form-group .validation-error ~ .validation-error,
.validation-error + .validation-error,
div:has(+ .validation-error) + .validation-error {
    display: none !important;
}

/* Ensure only first error message shows */
.mb-4 .validation-error:not(:first-of-type) {
    display: none !important;
}

/* Prevent duplicate error messages more aggressively */
.validation-error {
    display: block !important;
}

.mb-4 > div:has(.validation-error) .validation-error:not(:first-child) {
    display: none !important;
}

/* Hide browser validation messages */
input:invalid:not(:focus):not(:placeholder-shown) {
    box-shadow: none !important;
}

/* Prevent duplicate email error messages - ULTRA AGGRESSIVE */
input#email + .validation-error ~ .validation-error,
input#email ~ .validation-error ~ .validation-error,
.mb-4:has(input#email) .validation-error:not(:first-of-type),
.mb-4:has(input#email) .validation-error.email-error:not(:first-of-type),
.mb-4:has(input#email) .validation-error[data-error-type="email"]:not(:first-of-type) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

/* More aggressive duplicate hiding - ALL validation errors */
.mb-4 .validation-error:nth-of-type(n+2),
.mb-4 .validation-error:not(:first-of-type),
.mb-4 .validation-error ~ .validation-error {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

/* Ensure only one error per field - ULTRA STRICT */
.mb-4 > div:has(.validation-error) .validation-error:not(:first-child),
.mb-4 > .validation-error:not(:first-of-type),
.mb-4 .validation-error + .validation-error {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

/* Hide ALL duplicates except the first one */
.validation-error:not(:first-of-type) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

/* Hide browser validation messages */
input:invalid,
input:invalid:focus,
input:invalid:not(:focus) {
    box-shadow: none !important;
    outline: none !important;
}

/* Hide browser tooltips */
input::-webkit-validation-bubble-message,
input::-webkit-validation-bubble-icon {
    display: none !important;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.input-group .btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5em 0.75em;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #e3e6f0;
}

.btn {
    border-radius: 0.35rem;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-1px);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
    border: none;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #545b62 0%, #3d4449 100%);
    transform: translateY(-1px);
}

/* Password strength indicator */
.password-strength .progress-bar {
    transition: all 0.3s ease;
}

.password-strength .progress-bar.bg-danger {
    background-color: #dc3545 !important;
}

.password-strength .progress-bar.bg-warning {
    background-color: #ffc107 !important;
}

.password-strength .progress-bar.bg-success {
    background-color: #28a745 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userCreateForm');
    
    // Prevent browser's native validation completely
    if (form) {
        form.setAttribute('novalidate', 'novalidate');
        form.noValidate = true;
        
        // Remove required attributes to prevent browser validation
        form.querySelectorAll('input[required], select[required]').forEach(field => {
            field.removeAttribute('required');
        });
        
        // Clear default values from email and password fields
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');
        
        if (emailField) {
            // Force clear the field if it contains invalid value
            const emailValue = emailField.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            // Clear if empty, invalid format, or contains invalid characters like "oo", "001", etc.
            if (!emailValue || 
                !emailValue.includes('@') || 
                !emailRegex.test(emailValue) ||
                emailValue.length < 5 || // Minimum email length
                /^(oo|001|000|123|test|admin)$/i.test(emailValue)) { // Common invalid values
                emailField.value = '';
            }
            
            emailField.setAttribute('autocomplete', 'off');
            emailField.setAttribute('type', 'text'); // Change to text to prevent browser validation
            emailField.setAttribute('inputmode', 'email'); // For mobile keyboards
            
            // Clear on focus if it contains invalid value
            emailField.addEventListener('focus', function() {
                const val = this.value.trim();
                if (val && (!val.includes('@') || !emailRegex.test(val))) {
                    this.value = '';
                }
            });
            
            // Clear on blur if invalid
            emailField.addEventListener('blur', function() {
                const val = this.value.trim();
                if (val && (!val.includes('@') || !emailRegex.test(val))) {
                    this.value = '';
                }
            });
        }
        
        if (passwordField) {
            passwordField.value = '';
            passwordField.setAttribute('autocomplete', 'new-password');
        }
        
        if (passwordConfirmField) {
            passwordConfirmField.value = '';
            passwordConfirmField.setAttribute('autocomplete', 'new-password');
        }
        
        // Prevent browser validation tooltips
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('invalid', function(e) {
                e.preventDefault();
                this.setCustomValidity('');
            });
            
            // Clear browser validation messages on input
            field.addEventListener('input', function() {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            });
        });
    }
    
    // Hide duplicate validation messages on page load and continuously - ULTRA AGGRESSIVE
    function hideDuplicateValidationErrors() {
        // Remove all duplicate validation errors - ULTRA STRICT
        const validationErrors = document.querySelectorAll('.validation-error');
        const seenErrors = new Map();
        const errorCounts = new Map();
        
        // First pass: count errors by parent and text
        validationErrors.forEach((error) => {
            const parent = error.closest('.mb-4') || error.parentElement;
            const errorText = error.textContent.trim().toLowerCase();
            const key = (parent.id || parent.className || 'default') + '_' + errorText;
            
            if (!errorCounts.has(key)) {
                errorCounts.set(key, 0);
            }
            errorCounts.set(key, errorCounts.get(key) + 1);
        });
        
        // Second pass: keep only first, remove all others
        validationErrors.forEach((error, index) => {
            const parent = error.closest('.mb-4') || error.parentElement;
            const errorText = error.textContent.trim().toLowerCase();
            const key = (parent.id || parent.className || 'default') + '_' + errorText;
            
            // Get all siblings in this parent
            const siblings = Array.from(parent.querySelectorAll('.validation-error'));
            const firstIndex = siblings.indexOf(error);
            
            // If this is not the first error in this parent, remove it
            if (firstIndex > 0) {
                error.style.display = 'none';
                error.style.visibility = 'hidden';
                error.style.opacity = '0';
                error.style.height = '0';
                error.style.margin = '0';
                error.style.padding = '0';
                error.style.overflow = 'hidden';
                error.remove();
            } else if (errorCounts.get(key) > 1) {
                // If we've seen this exact error before, remove it
                if (seenErrors.has(key)) {
                    error.style.display = 'none';
                    error.style.visibility = 'hidden';
                    error.style.opacity = '0';
                    error.style.height = '0';
                    error.style.margin = '0';
                    error.style.padding = '0';
                    error.style.overflow = 'hidden';
                    error.remove();
                } else {
                    seenErrors.set(key, true);
                }
            }
        });
        
        // Third pass: remove duplicates by text content globally
        const allErrors = Array.from(document.querySelectorAll('.validation-error'));
        const errorTexts = new Map();
        allErrors.forEach((error, index) => {
            const text = error.textContent.trim().toLowerCase();
            if (errorTexts.has(text)) {
                // Remove this duplicate
                error.style.display = 'none';
                error.style.visibility = 'hidden';
                error.style.opacity = '0';
                error.style.height = '0';
                error.style.margin = '0';
                error.style.padding = '0';
                error.style.overflow = 'hidden';
                error.remove();
            } else {
                errorTexts.set(text, error);
            }
        });
        
        // Fourth pass: specifically for email errors
        const emailErrors = document.querySelectorAll('.validation-error.email-error, .mb-4:has(input#email) .validation-error');
        emailErrors.forEach((error, index) => {
            if (index > 0) {
                error.style.display = 'none';
                error.style.visibility = 'hidden';
                error.style.opacity = '0';
                error.style.height = '0';
                error.style.margin = '0';
                error.style.padding = '0';
                error.style.overflow = 'hidden';
                error.remove();
            }
        });
    }
    
    // Run immediately and on DOM changes
    hideDuplicateValidationErrors();
    setTimeout(hideDuplicateValidationErrors, 100);
    setTimeout(hideDuplicateValidationErrors, 500);
    
    // Also run when form is submitted
    if (form) {
        form.addEventListener('submit', function() {
            setTimeout(hideDuplicateValidationErrors, 50);
        });
    }
    
    // Observe DOM changes to hide duplicates
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                hideDuplicateValidationErrors();
            }
        });
    });
    
    if (form) {
        observer.observe(form, {
            childList: true,
            subtree: true
        });
    }
    
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    const progressBar = passwordStrength ? passwordStrength.querySelector('.progress-bar') : null;
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = '';
        
        if (password.length >= 8) strength += 1;
        if (password.match(/[a-z]/)) strength += 1;
        if (password.match(/[A-Z]/)) strength += 1;
        if (password.match(/[0-9]/)) strength += 1;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
        
        const percentage = (strength / 5) * 100;
        
        if (strength < 2) {
            feedback = 'Très faible';
            progressBar.className = 'progress-bar bg-danger';
        } else if (strength < 3) {
            feedback = 'Faible';
            progressBar.className = 'progress-bar bg-warning';
        } else if (strength < 4) {
            feedback = 'Moyen';
            progressBar.className = 'progress-bar bg-warning';
        } else {
            feedback = 'Fort';
            progressBar.className = 'progress-bar bg-success';
        }
        
        progressBar.style.width = percentage + '%';
        strengthText.textContent = `Force du mot de passe: ${feedback}`;
        
        return strength >= 3;
    }
    
    // Password visibility toggle
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + 'Icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };
    
    // Real-time password strength checking
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length > 0) {
            passwordStrength.style.display = 'block';
            checkPasswordStrength(password);
        } else {
            passwordStrength.style.display = 'none';
        }
    });
    
    // Password confirmation validation
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmation = this.value;
        
        if (confirmation.length > 0) {
            if (password === confirmation) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Enhanced form validation
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        const fieldType = field.type;
        
        // Remove existing validation classes
        field.classList.remove('is-valid', 'is-invalid');
        
        if (field.hasAttribute('required') && value === '') {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Specific validations
        if (fieldName === 'email' && value) {
            // Clear any existing validation error messages first
            const emailContainer = field.closest('.mb-4');
            if (emailContainer) {
                const existingErrors = emailContainer.querySelectorAll('.validation-error');
                existingErrors.forEach((error, idx) => {
                    if (idx > 0) {
                        error.remove();
                    }
                });
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.classList.add('is-invalid');
                return false;
            } else {
                field.classList.remove('is-invalid');
                field.setCustomValidity('');
            }
        }
        
        if (fieldName === 'ppr' && value) {
            const pprRegex = /^[0-9]{8}$/;
            if (!pprRegex.test(value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }
        
        if (fieldName === 'password' && value) {
            if (value.length < 8) {
                field.classList.add('is-invalid');
                return false;
            }
            if (!checkPasswordStrength(value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }
        
        if (fieldName === 'password_confirmation' && value) {
            if (value !== passwordInput.value) {
                field.classList.add('is-invalid');
                return false;
            }
        }
        
        if (value !== '') {
            field.classList.add('is-valid');
        }
        
        return true;
    }
    
    // Real-time validation
    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
    
    // Simple form validation only - prevent duplicate messages
    form.addEventListener('submit', function(e) {
        // Remove all previous validation error classes first
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        // Hide any duplicate browser validation messages
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.setCustomValidity('');
        });
        
        // Hide duplicate validation error messages - remove duplicates
        const validationErrors = form.querySelectorAll('.validation-error');
        validationErrors.forEach((error, index) => {
            const parent = error.parentElement;
            const siblings = Array.from(parent.querySelectorAll('.validation-error'));
            const firstIndex = siblings.indexOf(error);
            if (firstIndex > 0) {
                error.style.display = 'none';
                error.remove(); // Remove duplicate elements
            }
        });
        
        // Also hide duplicate errors using CSS
        hideDuplicateValidationErrors();
        
        // Basic validation - only check required fields
        const requiredFields = [
            { id: 'name', name: 'Nom complet' },
            { id: 'email', name: 'Email' },
            { id: 'ppr', name: 'PPR' },
            { id: 'password', name: 'Mot de passe' },
            { id: 'password_confirmation', name: 'Confirmation du mot de passe' }
        ];
        
        let isValid = true;
        const firstInvalid = [];
        
        requiredFields.forEach(fieldInfo => {
            const field = document.getElementById(fieldInfo.id);
            if (field && !field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid.length) {
                    firstInvalid.push({ field: field, name: fieldInfo.name });
                }
            }
        });
        
        // Validate email format
        const emailField = document.getElementById('email');
        if (emailField) {
            const emailValue = emailField.value.trim();
            
            // Clear any previous validation
            emailField.classList.remove('is-invalid');
            emailField.setCustomValidity('');
            
            // Remove any existing error messages for email
            const emailContainer = emailField.closest('.mb-4');
            if (emailContainer) {
                const existingErrors = emailContainer.querySelectorAll('.validation-error');
                existingErrors.forEach((error, index) => {
                    if (index > 0) {
                        error.remove();
                    }
                });
            }
            
            if (!emailValue) {
                emailField.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid.length) {
                    firstInvalid.push({ field: emailField, name: 'Email' });
                }
            } else {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailValue)) {
                    emailField.classList.add('is-invalid');
                    isValid = false;
                    if (!firstInvalid.length) {
                        firstInvalid.push({ field: emailField, name: 'Email' });
                    }
                } else {
                    emailField.classList.remove('is-invalid');
                    emailField.setCustomValidity('');
                }
            }
        }
        
        // Validate PPR format (8 digits)
        const pprField = document.getElementById('ppr');
        if (pprField && pprField.value.trim()) {
            const pprRegex = /^[0-9]{8}$/;
            if (!pprRegex.test(pprField.value.trim())) {
                pprField.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid.length) {
                    firstInvalid.push({ field: pprField, name: 'PPR' });
                }
            }
        }
        
        // Check password confirmation
        if (passwordInput && confirmPasswordInput && passwordInput.value.trim()) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid.length) {
                    firstInvalid.push({ field: confirmPasswordInput, name: 'Confirmation du mot de passe' });
                }
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            // Only show one alert, not multiple
            if (firstInvalid.length > 0) {
                firstInvalid[0].field.focus();
                firstInvalid[0].field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
        
        // Allow normal form submission
        return true;
    });
    
    // Simple input handling
    form.addEventListener('input', function() {
        // Basic input handling without auto-save
    });
    
    // Enhanced PPR input formatting
    const pprInput = document.getElementById('ppr');
    pprInput.addEventListener('input', function() {
        // Only allow numbers
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 8 digits
        if (this.value.length > 8) {
            this.value = this.value.substring(0, 8);
        }
    });
    
    // Enhanced email validation with domain suggestions
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        const email = this.value;
        if (email && !email.includes('@')) {
            // Could add domain suggestions here
        }
    });
});
</script>
@endpush

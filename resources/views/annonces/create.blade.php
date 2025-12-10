@extends('layouts.app')

@section('title', 'Créer une Annonce')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Content -->
    <div class="mb-8">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/20 p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-plus text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Créer une Annonce</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-lg mt-2">Publiez une nouvelle annonce</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('annonces.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
        <form action="{{ route('annonces.store') }}" method="POST" enctype="multipart/form-data" id="annonceForm" novalidate autocomplete="off">
            @csrf

            <div class="space-y-6">
                <!-- PPR -->
                <div>
                    <label for="ppr" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        PPR (optionnel)
                    </label>
                    <select 
                        id="ppr" 
                        name="ppr"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('ppr') border-red-500 @enderror">
                        <option value="">Utiliser mon PPR ({{ auth()->user()->ppr }})</option>
                        @foreach($users as $user)
                            <option value="{{ $user->ppr }}" {{ old('ppr') == $user->ppr ? 'selected' : '' }}>
                                {{ $user->ppr }} - {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('ppr')
                        <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle"></i> Si vide, votre PPR sera utilisé par défaut
                    </p>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-align-left text-blue-500 mr-2"></i>
                        Contenu de l'annonce <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="8"
                        minlength="10"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('content') border-red-500 @enderror"
                        placeholder="Saisissez le contenu de l'annonce...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Minimum 10 caractères requis</p>
                </div>

                <!-- Type -->
                <div>
                    <label for="type_annonce_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-tag text-blue-500 mr-2"></i>
                        Type d'annonce <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="type_annonce_id" 
                        name="type_annonce_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type_annonce_id') border-red-500 @enderror">
                        <option value="">Sélectionner un type d'annonce</option>
                        @foreach($typesAnnonces as $type)
                            <option value="{{ $type->id }}" {{ old('type_annonce_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_annonce_id')
                        <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-toggle-on text-blue-500 mr-2"></i>
                        Statut
                    </label>
                    <select 
                        id="statut" 
                        name="statut"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('statut') border-red-500 @enderror">
                        <option value="active" {{ old('statut', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('statut') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('statut')
                        <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Entités -->
                <div>
                    <label for="entites" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-building text-blue-500 mr-2"></i>
                        Entités <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="entites" 
                        name="entites[]"
                        multiple
                        size="5"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('entites') border-red-500 @enderror">
                        @foreach($entites as $entite)
                            <option value="{{ $entite->id }}" {{ in_array($entite->id, old('entites', [])) ? 'selected' : '' }}>
                                {{ $entite->nom ?? $entite->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('entites')
                        <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle"></i> Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs entités. Au moins une entité est requise.
                    </p>
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-image text-blue-500 mr-2"></i>
                        Image (optionnel)
                    </label>
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/jpeg,image/png,image/jpg,image/gif"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('image') border-red-500 @enderror">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('annonces.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 shadow-lg">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Créer l'Annonce
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Hide duplicate validation messages - ULTRA STRONG */
.validation-error:not(:first-of-type),
.space-y-6 > div .validation-error:not(:first-of-type),
.mb-4 .validation-error:not(:first-of-type) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

/* More aggressive duplicate hiding */
.space-y-6 > div .validation-error + .validation-error,
.space-y-6 > div .validation-error ~ .validation-error {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* Hide browser validation messages */
input:invalid:not(:focus):not(:placeholder-shown),
textarea:invalid:not(:focus):not(:placeholder-shown),
select:invalid:not(:focus) {
    box-shadow: none !important;
}

/* Prevent browser validation tooltips */
input::-webkit-validation-bubble-message,
textarea::-webkit-validation-bubble-message,
select::-webkit-validation-bubble-message {
    display: none !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('annonceForm');
    
    if (form) {
        // Remove required attributes to prevent browser validation
        form.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.removeAttribute('required');
        });
        
        // Prevent browser validation tooltips
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('invalid', function(e) {
                e.preventDefault();
                this.setCustomValidity('');
            });
        });
        
        // Custom validation on submit
        form.addEventListener('submit', function(e) {
            const ppr = document.getElementById('ppr').value.trim();
            const content = document.getElementById('content').value.trim();
            
            let isValid = true;
            let firstInvalid = null;
            
            // Validate Type
            const typeAnnonceId = document.getElementById('type_annonce_id').value.trim();
            if (!typeAnnonceId) {
                isValid = false;
                const typeField = document.getElementById('type_annonce_id');
                typeField.classList.add('border-red-500');
                if (!firstInvalid) firstInvalid = typeField;
            } else {
                document.getElementById('type_annonce_id').classList.remove('border-red-500');
            }
            
            // Validate Content
            if (!content || content.length < 10) {
                isValid = false;
                const contentField = document.getElementById('content');
                contentField.classList.add('border-red-500');
                if (!firstInvalid) firstInvalid = contentField;
            } else {
                document.getElementById('content').classList.remove('border-red-500');
            }
            
            // Validate Entites (required)
            const entitesSelect = document.getElementById('entites');
            const selectedEntites = Array.from(entitesSelect.selectedOptions).map(option => option.value);
            if (selectedEntites.length === 0) {
                isValid = false;
                entitesSelect.classList.add('border-red-500');
                if (!firstInvalid) firstInvalid = entitesSelect;
            } else {
                entitesSelect.classList.remove('border-red-500');
            }
            
            if (!isValid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
                
                // Hide duplicate validation messages
                const validationErrors = form.querySelectorAll('.validation-error');
                validationErrors.forEach((error, index) => {
                    if (index > 0) {
                        error.style.display = 'none';
                    }
                });
                
                return false;
            }
        });
        
        // Hide duplicate validation messages on page load and continuously
        function hideDuplicateValidationErrors() {
            const validationErrors = form.querySelectorAll('.validation-error');
            validationErrors.forEach((error, index) => {
                const parent = error.parentElement;
                const siblings = Array.from(parent.querySelectorAll('.validation-error'));
                const firstIndex = siblings.indexOf(error);
                if (firstIndex > 0) {
                    error.style.display = 'none';
                    error.remove();
                }
            });
        }
        
        // Run immediately and on DOM changes
        hideDuplicateValidationErrors();
        setTimeout(hideDuplicateValidationErrors, 100);
        setTimeout(hideDuplicateValidationErrors, 500);
        
        // Observe DOM changes to hide duplicates
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    hideDuplicateValidationErrors();
                }
            });
        });
        
        observer.observe(form, {
            childList: true,
            subtree: true
        });
    }
});
</script>
@endpush
@endsection


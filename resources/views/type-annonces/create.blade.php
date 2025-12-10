@extends('layouts.app')

@section('title', 'Créer un Type d\'Annonce')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-tags text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                    Créer un Type d'Annonce
                </h1>
                <p class="text-gray-600 text-lg mt-2">Ajoutez un nouveau type d'annonce au système</p>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/20">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-tags text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Formulaire de création</h2>
                <p class="text-gray-600">Remplissez les informations pour créer un nouveau type d'annonce</p>
            </div>
        </div>
        <form action="{{ route('type-annonces.store') }}" method="POST" class="space-y-8" novalidate autocomplete="off">
            @csrf
            
            <!-- Type Info -->
            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 border border-orange-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-orange-900">Informations du Type</h3>
                </div>
                
                <div class="form-group mb-4">
                    <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 hover:border-gray-400 @error('nom') border-red-500 @enderror" 
                           id="nom" name="nom" value="{{ old('nom') }}" 
                           placeholder="Ex: Information générale, Urgent, etc.">
                    @error('nom')
                        <div class="text-red-500 text-sm mt-1 flex items-center gap-2 validation-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea 
                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 hover:border-gray-400 @error('description') border-red-500 @enderror" 
                        id="description" name="description" rows="3"
                        placeholder="Description du type d'annonce...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-red-500 text-sm mt-1 flex items-center gap-2 validation-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="couleur" class="block text-sm font-semibold text-gray-700 mb-2">
                        Couleur
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" 
                               class="w-16 h-12 border border-gray-300 rounded-xl cursor-pointer @error('couleur') border-red-500 @enderror" 
                               id="couleur" name="couleur" value="{{ old('couleur', '#ff6b35') }}">
                        <input type="text" 
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 hover:border-gray-400 @error('couleur') border-red-500 @enderror" 
                               id="couleur_text" 
                               value="{{ old('couleur', '#ff6b35') }}"
                               placeholder="#ff6b35"
                               pattern="^#[0-9A-Fa-f]{6}$">
                    </div>
                    @error('couleur')
                        <div class="text-red-500 text-sm mt-1 flex items-center gap-2 validation-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle"></i> Choisissez une couleur pour identifier ce type d'annonce
                    </div>
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-sm font-semibold text-gray-700">Actif</span>
                    </label>
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle"></i> Seuls les types actifs seront disponibles lors de la création d'annonces
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('type-annonces.index') }}" 
                   class="inline-flex items-center gap-3 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-300">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-save"></i>
                    <span class="font-semibold">Créer le Type</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.validation-error {
    display: block !important;
}

.validation-error:not(:first-of-type) {
    display: none !important;
}

input:invalid:not(:focus):not(:placeholder-shown),
select:invalid:not(:focus):not(:placeholder-shown),
textarea:invalid:not(:focus):not(:placeholder-shown) {
    border-color: #dc3545;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('couleur');
    const colorText = document.getElementById('couleur_text');
    
    // Sync color picker with text input
    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
    });
    
    colorText.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorInput.value = this.value;
        }
    });
    
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const nomField = document.getElementById('nom');
        let isValid = true;
        
        if (!nomField.value.trim()) {
            nomField.classList.add('border-red-500');
            isValid = false;
        } else {
            nomField.classList.remove('border-red-500');
        }
        
        if (!isValid) {
            e.preventDefault();
            nomField.focus();
            nomField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        
        return true;
    });
});
</script>
@endpush



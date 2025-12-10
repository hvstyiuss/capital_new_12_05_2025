@extends('layouts.app')

@section('title', 'Paramètres du Compte')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-cog text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Paramètres du Compte
                </h1>
                <p class="text-gray-600 text-lg mt-2">Gérez vos paramètres de compte et préférences</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 shadow-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-xl"></i>
                <div>
                    <p class="font-semibold mb-1">Erreur de validation</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px" aria-label="Tabs">
                <button onclick="showTab('personal-info')" id="tab-personal-info" class="tab-button active px-6 py-4 text-sm font-medium text-center border-b-2 border-purple-600 text-purple-600">
                    <i class="fas fa-user mr-2"></i>
                    Informations Personnelles
                </button>
                <button onclick="showTab('password')" id="tab-password" class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-lock mr-2"></i>
                    Mot de Passe
                </button>
                <button onclick="showTab('notifications')" id="tab-notifications" class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-8">
            <!-- Personal Info Tab -->
            <div id="content-personal-info" class="tab-content">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Informations Personnelles</h2>
                        <p class="text-gray-600">Mettez à jour vos informations de profil</p>
                    </div>
                </div>

                <form action="{{ route('account-settings.update-personal-info') }}" method="POST" class="space-y-6" novalidate autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Profile Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image me-2 text-purple-500"></i>
                            Photo de profil
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 relative">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" 
                                         alt="Photo actuelle" 
                                         id="current_image_preview"
                                         class="w-20 h-20 rounded-xl object-cover border-2 border-purple-300 shadow-md">
                                @elseif($user->userInfo && $user->userInfo->photo)
                                    <img src="{{ asset('storage/' . $user->userInfo->photo) }}" 
                                         alt="Photo actuelle" 
                                         id="current_image_preview"
                                         class="w-20 h-20 rounded-xl object-cover border-2 border-purple-300 shadow-md">
                                @else
                                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center border-2 border-purple-300 shadow-md">
                                        <span class="text-white text-xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <img src="" alt="Aperçu" id="image_preview" class="w-20 h-20 rounded-xl object-cover border-2 border-purple-500 shadow-md hidden mt-2">
                                @if($user->image || ($user->userInfo && $user->userInfo->photo))
                                <button type="button" 
                                        onclick="deleteProfileImage()" 
                                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transition-colors"
                                        title="Supprimer la photo">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 hover:border-gray-400 @error('image') border-red-500 @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</p>
                            </div>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('name') border-red-500 @enderror"
                                   autocomplete="name">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email ?? ($user->userInfo->email ?? '')) }}"
                                   placeholder="Entrez votre email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('email') border-red-500 @enderror"
                                   autocomplete="email">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Tab -->
            <div id="content-password" class="tab-content hidden">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lock text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Modifier le Mot de Passe</h2>
                        <p class="text-gray-600">Changez votre mot de passe pour sécuriser votre compte</p>
                    </div>
                </div>

                <form action="{{ route('account-settings.update-password') }}" method="POST" class="space-y-6" novalidate autocomplete="off" id="passwordForm">
                    @csrf
                    
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Mot de passe actuel <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('current_password') border-red-500 @enderror"
                               autocomplete="current-password">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nouveau mot de passe <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('new_password') border-red-500 @enderror"
                                   minlength="8"
                                   autocomplete="new-password">
                            @error('new_password')
                                <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Minimum 8 caractères</p>
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirmer le nouveau mot de passe <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('new_password_confirmation') border-red-500 @enderror"
                                   minlength="8"
                                   autocomplete="new-password">
                            @error('new_password_confirmation')
                                <p class="text-red-500 text-sm mt-1 validation-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-key mr-2"></i>
                            Modifier le mot de passe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Tab -->
            <div id="content-notifications" class="tab-content hidden">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Préférences de Notification</h2>
                        <p class="text-gray-600">Configurez comment vous souhaitez recevoir les notifications</p>
                    </div>
                </div>

                <form action="{{ route('account-settings.update-notifications') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope text-blue-600 text-xl"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Notifications par Email</h3>
                                    <p class="text-sm text-gray-600">Recevez les notifications importantes par email</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="email_notifications" 
                                       value="1"
                                       {{ $user->email_notifications ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Notifications Push</h3>
                                    <p class="text-sm text-gray-600">Recevez des notifications push sur votre appareil</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="push_notifications" 
                                       value="1"
                                       {{ $user->push_notifications ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-amber-600 text-white rounded-xl hover:from-yellow-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Enregistrer les préférences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .tab-button {
        transition: all 0.3s ease;
    }

    .tab-button.active {
        color: #9333ea;
        border-bottom-color: #9333ea;
    }

    .tab-content {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Prevent duplicate validation messages */
    .validation-error {
        margin-top: 0.25rem;
    }
    
    .validation-error:not(:first-of-type) {
        display: none !important;
    }
    
    /* Hide browser validation tooltips */
    input:invalid {
        box-shadow: none;
    }
    
    input:invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    /* Remove duplicate error messages */
    .form-group .validation-error + .validation-error,
    .validation-error + .validation-error {
        display: none !important;
    }
    
    /* Ensure validation errors are unique */
    div:has(+ .validation-error) + .validation-error {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.classList.add('text-gray-500', 'border-transparent');
        button.classList.remove('text-purple-600', 'border-purple-600');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active class to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('active', 'text-purple-600', 'border-purple-600');
    activeTab.classList.remove('text-gray-500', 'border-transparent');
}

function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const currentPreview = document.getElementById('current_image_preview');
    const parentDiv = currentPreview ? currentPreview.parentElement : null;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (currentPreview) {
                currentPreview.classList.add('hidden');
            }
            if (parentDiv) {
                const placeholder = parentDiv.querySelector('.w-20.h-20.rounded-xl.bg-gradient-to-br');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
        if (currentPreview) {
            currentPreview.classList.remove('hidden');
        }
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[novalidate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const firstInvalid = [];

            // Password form specific validation
            if (form.id === 'passwordForm') {
                const currentPassword = form.querySelector('#current_password');
                const newPassword = form.querySelector('#new_password');
                const confirmPassword = form.querySelector('#new_password_confirmation');
                
                // Clear previous errors
                [currentPassword, newPassword, confirmPassword].forEach(input => {
                    if (input) {
                        input.classList.remove('border-red-500');
                        input.setCustomValidity('');
                    }
                });
                
                // Validate current password if new password is provided
                if (newPassword && newPassword.value.trim()) {
                    if (!currentPassword || !currentPassword.value.trim()) {
                        isValid = false;
                        if (currentPassword) {
                            currentPassword.classList.add('border-red-500');
                            currentPassword.setCustomValidity('Le mot de passe actuel est requis');
                            firstInvalid.push(currentPassword);
                        }
                    }
                }
                
                // Validate new password
                if (newPassword && newPassword.value.trim()) {
                    if (newPassword.value.length < 8) {
                        isValid = false;
                        newPassword.classList.add('border-red-500');
                        newPassword.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères');
                        if (!firstInvalid.length) firstInvalid.push(newPassword);
                    } else {
                        newPassword.setCustomValidity('');
                    }
                }
                
                // Validate password confirmation
                if (newPassword && confirmPassword && newPassword.value.trim()) {
                    if (newPassword.value !== confirmPassword.value) {
                        isValid = false;
                        confirmPassword.classList.add('border-red-500');
                        confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
                        if (!firstInvalid.length) firstInvalid.push(confirmPassword);
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                }
            } else {
                // Other forms validation
                const inputs = form.querySelectorAll('input[required]');
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        input.setCustomValidity('Ce champ est requis');
                        if (!firstInvalid.length) firstInvalid.push(input);
                    } else {
                        input.classList.remove('border-red-500');
                        input.setCustomValidity('');
                    }
                });
            }

            if (!isValid) {
                e.preventDefault();
                if (firstInvalid.length > 0) {
                    firstInvalid[0].focus();
                    firstInvalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
    
    // Prevent duplicate validation error messages
    const validationErrors = document.querySelectorAll('.validation-error');
    validationErrors.forEach((error, index) => {
        if (index > 0 && error.previousElementSibling && error.previousElementSibling.classList.contains('validation-error')) {
            error.style.display = 'none';
        }
    });
});

function deleteProfileImage() {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        fetch('{{ route("account-settings.delete-profile-image") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove image preview
                const currentPreview = document.getElementById('current_image_preview');
                const imagePreview = document.getElementById('image_preview');
                const parentDiv = currentPreview ? currentPreview.parentElement : null;
                
                if (currentPreview) {
                    currentPreview.remove();
                }
                if (imagePreview) {
                    imagePreview.classList.add('hidden');
                }
                
                // Show placeholder
                if (parentDiv) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'w-20 h-20 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center border-2 border-purple-300 shadow-md';
                    placeholder.innerHTML = '<span class="text-white text-xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>';
                    parentDiv.insertBefore(placeholder, parentDiv.firstChild);
                }
                
                // Remove delete button
                const deleteBtn = document.querySelector('button[onclick="deleteProfileImage()"]');
                if (deleteBtn) {
                    deleteBtn.remove();
                }
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 shadow-lg';
                successMsg.innerHTML = '<div class="flex items-center gap-3"><i class="fas fa-check-circle text-xl"></i><p class="font-semibold">' + data.message + '</p></div>';
                document.querySelector('.container').insertBefore(successMsg, document.querySelector('.container').firstChild);
                
                // Remove success message after 5 seconds
                setTimeout(() => {
                    successMsg.remove();
                }, 5000);
            } else {
                alert('Erreur: ' + (data.message || 'Impossible de supprimer la photo'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la suppression de la photo');
        });
    }
}
</script>
@endpush
@endsection


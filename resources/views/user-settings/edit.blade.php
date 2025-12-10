@extends('layouts.app')

@section('title', 'Modifier les Paramètres')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                    Modifier les Paramètres
                </h1>
                <p class="text-gray-600 text-lg mt-2">Personnalisez vos préférences</p>
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

    <!-- Edit Form -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20">
        <div class="p-8">
            <form action="{{ route('user-settings.update') }}" method="POST" class="space-y-8" id="settingsForm">
                @csrf
                @method('PUT')

                <!-- Language & Theme -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Language -->
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-200">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-language text-blue-600 text-xl"></i>
                            <h3 class="text-lg font-semibold text-gray-900">Langue</h3>
                        </div>
                        <select name="language" id="language" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('language') border-red-500 @enderror">
                            <option value="fr" {{ $settings->language === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="ar" {{ $settings->language === 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                        @error('language')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Theme -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-palette text-purple-600 text-xl"></i>
                            <h3 class="text-lg font-semibold text-gray-900">Thème</h3>
                        </div>
                        <select name="theme" id="theme" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('theme') border-red-500 @enderror">
                            <option value="light" {{ $settings->theme === 'light' ? 'selected' : '' }}>Clair</option>
                            <option value="dark" {{ $settings->theme === 'dark' ? 'selected' : '' }}>Sombre</option>
                            <option value="auto" {{ $settings->theme === 'auto' ? 'selected' : '' }}>Automatique</option>
                        </select>
                        @error('theme')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Timezone -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-clock text-green-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Fuseau Horaire</h3>
                    </div>
                    <select name="timezone" id="timezone" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('timezone') border-red-500 @enderror">
                        <option value="Africa/Casablanca" {{ $settings->timezone === 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca (GMT+1)</option>
                        <option value="UTC" {{ $settings->timezone === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                        <option value="Europe/Paris" {{ $settings->timezone === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (GMT+1)</option>
                        <option value="America/New_York" {{ $settings->timezone === 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
                    </select>
                    @error('timezone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notifications -->
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-200">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fas fa-bell text-yellow-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                    </div>
                    <div class="space-y-4">
                        <!-- Email Notifications -->
                        <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-yellow-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope text-yellow-600 text-xl"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Notifications par Email</h4>
                                    <p class="text-sm text-gray-600">Recevez les notifications importantes par email</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="notifications_email" value="1" {{ $settings->notifications_email ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                            </label>
                        </div>

                        <!-- SMS Notifications -->
                        <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-yellow-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-sms text-indigo-600 text-xl"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Notifications SMS</h4>
                                    <p class="text-sm text-gray-600">Recevez des notifications par SMS</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="notifications_sms" value="1" {{ $settings->notifications_sms ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Appearance & Security -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dark Mode -->
                    <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-{{ $settings->dark_mode ? 'moon' : 'sun' }} text-gray-600 text-xl"></i>
                            <h3 class="text-lg font-semibold text-gray-900">Mode Sombre</h3>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-200">
                            <span class="text-gray-700">Activer le mode sombre</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="dark_mode" value="1" {{ $settings->dark_mode ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Two Factor Authentication -->
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl p-6 border border-red-200">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                            <h3 class="text-lg font-semibold text-gray-900">Authentification à Deux Facteurs</h3>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-red-200">
                            <span class="text-gray-700">Activer la 2FA</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="two_factor_enabled" value="1" {{ $settings->two_factor_enabled ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('user-settings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox values for form submission
    const form = document.getElementById('settingsForm');
    form.addEventListener('submit', function(e) {
        // Ensure unchecked checkboxes send false value
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = checkbox.name;
                hiddenInput.value = '0';
                form.appendChild(hiddenInput);
            }
        });
    });

    // Real-time dark mode toggle preview
    const darkModeToggle = document.querySelector('input[name="dark_mode"]');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function() {
            // This is just a preview - actual implementation would be in your layout
            console.log('Dark mode toggled:', this.checked);
        });
    }
});
</script>
@endpush
@endsection
















@extends('layouts.app')

@section('title', 'Paramètres Utilisateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-cog text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                    Paramètres Utilisateur
                </h1>
                <p class="text-gray-600 text-lg mt-2">Gérez vos préférences et paramètres personnels</p>
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

    <!-- Settings Card -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20">
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-cog text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Vos Paramètres</h2>
                        <p class="text-gray-600">Personnalisez votre expérience</p>
                    </div>
                </div>
                <a href="{{ route('user-settings.edit') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
            </div>

            <!-- Settings Display -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Language -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-language text-blue-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Langue</h3>
                    </div>
                    <p class="text-gray-700 font-medium">
                        @if($settings->language === 'fr')
                            <i class="fas fa-flag mr-2"></i>Français
                        @elseif($settings->language === 'ar')
                            <i class="fas fa-flag mr-2"></i>العربية
                        @else
                            <i class="fas fa-flag mr-2"></i>English
                        @endif
                    </p>
                </div>

                <!-- Theme -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-palette text-purple-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Thème</h3>
                    </div>
                    <p class="text-gray-700 font-medium capitalize">
                        <i class="fas fa-{{ $settings->theme === 'dark' ? 'moon' : ($settings->theme === 'light' ? 'sun' : 'adjust') }} mr-2"></i>
                        {{ $settings->theme === 'dark' ? 'Sombre' : ($settings->theme === 'light' ? 'Clair' : 'Automatique') }}
                    </p>
                </div>

                <!-- Timezone -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-clock text-green-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Fuseau Horaire</h3>
                    </div>
                    <p class="text-gray-700 font-medium">{{ $settings->timezone }}</p>
                </div>

                <!-- Dark Mode -->
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-{{ $settings->dark_mode ? 'moon' : 'sun' }} text-gray-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Mode Sombre</h3>
                    </div>
                    <p class="text-gray-700 font-medium">
                        @if($settings->dark_mode)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Activé</span>
                        @else
                            <span class="text-gray-500"><i class="fas fa-times-circle mr-2"></i>Désactivé</span>
                        @endif
                    </p>
                </div>

                <!-- Email Notifications -->
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-envelope text-yellow-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Notifications Email</h3>
                    </div>
                    <p class="text-gray-700 font-medium">
                        @if($settings->notifications_email)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Activées</span>
                        @else
                            <span class="text-gray-500"><i class="fas fa-times-circle mr-2"></i>Désactivées</span>
                        @endif
                    </p>
                </div>

                <!-- SMS Notifications -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl p-6 border border-indigo-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-sms text-indigo-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Notifications SMS</h3>
                    </div>
                    <p class="text-gray-700 font-medium">
                        @if($settings->notifications_sms)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Activées</span>
                        @else
                            <span class="text-gray-500"><i class="fas fa-times-circle mr-2"></i>Désactivées</span>
                        @endif
                    </p>
                </div>

                <!-- Two Factor Authentication -->
                <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl p-6 border border-red-200">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Authentification à Deux Facteurs</h3>
                    </div>
                    <p class="text-gray-700 font-medium">
                        @if($settings->two_factor_enabled)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Activée</span>
                        @else
                            <span class="text-gray-500"><i class="fas fa-times-circle mr-2"></i>Désactivée</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
















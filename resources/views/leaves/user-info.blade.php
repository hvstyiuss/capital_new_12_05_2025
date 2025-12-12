@extends('layouts.app')

@section('title', 'Informations de l\'Agent')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                        Informations de l'Agent
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">Détails complets de l'agent et son parcours professionnel</p>
                </div>
                <button type="button" 
                        onclick="window.history.back()" 
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Information Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-id-card mr-2"></i>
                            Informations Personnelles
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-6">
                            @if($user->userInfo && $user->userInfo->photo)
                                <img src="{{ $user->userInfo->photo_url }}" 
                                     alt="{{ $user->fname }} {{ $user->lname }}" 
                                     class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-blue-100 shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full mx-auto mb-4 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center border-4 border-blue-100 shadow-lg">
                                    <span class="text-white text-4xl font-bold">
                                        {{ strtoupper(substr($user->fname, 0, 1) . substr($user->lname, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $user->fname }} {{ $user->lname }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-id-badge mr-1"></i>
                                    PPR: {{ $user->ppr }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($user->email)
                                            <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">
                                                {{ $user->email }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            @if($user->userInfo)
                                @if($user->userInfo->cin)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                        <i class="fas fa-id-card text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CIN</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $user->userInfo->cin }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($user->userInfo->corps)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                        <i class="fas fa-building text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Corps fonctionnel</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($user->userInfo->corps) }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($user->userInfo->grade)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                                        <i class="fas fa-star text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Grade</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->userInfo->grade->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($user->userInfo->adresse)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-red-600 dark:text-red-400"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresse</p>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->userInfo->adresse }}</p>
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parcours Professionnel Card -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-route mr-2"></i>
                            Parcours Professionnel
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($parcours->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Entité</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Poste</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Grade</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Début</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Fin</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Chef</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($parcours as $p)
                                            @php
                                                $dateFin = $p->date_fin ? \Carbon\Carbon::parse($p->date_fin) : null;
                                                $isActive = $dateFin === null || $dateFin->isFuture() || $dateFin->isToday();
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $isActive ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        @if($p->entite)
                                                            {{ $p->entite->name }}
                                                            @if($p->entite->parent)
                                                                <br><small class="text-gray-500 dark:text-gray-400">({{ $p->entite->parent->name }})</small>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-400">N/A</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-white">{{ $p->poste ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-white">{{ $p->grade ? $p->grade->name : 'N/A' }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                        @if($p->date_debut)
                                                            {{ \Carbon\Carbon::parse($p->date_debut)->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-gray-400">N/A</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm">
                                                        @if($p->date_fin)
                                                            {{ \Carbon\Carbon::parse($p->date_fin)->format('d/m/Y') }}
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                En cours
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    @if($isActive)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Actif
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-times-circle mr-1"></i>
                                                            Terminé
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    @php
                                                        $isChefParcours = $p->entite && $p->entite->chef_ppr === $p->ppr;
                                                    @endphp
                                                    @if($isChefParcours)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                            <i class="fas fa-crown mr-1"></i>
                                                            Oui
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Non
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Warning if user has multiple active posts -->
                            @php
                                $activeParcours = $parcours->filter(function($p) {
                                    $dateFin = $p->date_fin ? \Carbon\Carbon::parse($p->date_fin) : null;
                                    return $dateFin === null || $dateFin->isFuture() || $dateFin->isToday();
                                });
                                $activeChefParcours = $activeParcours->filter(function($p) {
                                    return $p->entite && $p->entite->chef_ppr === $p->ppr;
                                });
                            @endphp
                            
                            @if($activeParcours->count() > 1)
                                <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                Attention: Postes actifs multiples détectés
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                                <p>Cet utilisateur a {{ $activeParcours->count() }} poste(s) actif(s) en même temps, ce qui n'est pas autorisé. Veuillez terminer les postes précédents avant d'en créer de nouveaux.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($activeChefParcours->count() > 1)
                                <div class="mt-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                                Erreur: Chef dans plusieurs postes actifs
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                                <p>Cet utilisateur est chef dans {{ $activeChefParcours->count() }} poste(s) actif(s) en même temps, ce qui n'est pas autorisé. Un utilisateur ne peut être chef que dans un seul poste actif à la fois.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-lg">Aucun parcours professionnel trouvé</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Détails du Type d\'Annonce')

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
                    Détails du Type d'Annonce
                </h1>
                <p class="text-gray-600 text-lg mt-2">Informations complètes du type {{ $typeAnnonce->nom }}</p>
            </div>
        </div>
    </div>

    <!-- Type Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <x-card title="Informations du Type" variant="gradient" color="orange" icon="fas fa-info-circle">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nom</label>
                        <p class="text-lg font-bold text-orange-600">{{ $typeAnnonce->nom }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Description</label>
                        <p class="text-gray-700">{{ $typeAnnonce->description ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Couleur</label>
                        <div class="flex items-center gap-3">
                            <span class="inline-block w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: {{ $typeAnnonce->couleur ?: '#007bff' }}"></span>
                            <span class="text-gray-700 font-mono">{{ $typeAnnonce->couleur ?: '#007bff' }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Statut</label>
                        <p>
                            @if($typeAnnonce->is_active)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Date de création</label>
                        <p class="text-gray-700">{{ $typeAnnonce->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Dernière mise à jour</label>
                        <p class="text-gray-700">{{ $typeAnnonce->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Announcements -->
            <x-card title="Annonces de ce type" variant="gradient" color="blue" icon="fas fa-bullhorn" class="mt-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $typeAnnonce->annonces_count }}</div>
                    <p class="text-gray-600 mt-1">annonce(s) utilisant ce type</p>
                </div>
            </x-card>
        </div>

        <!-- Sidebar -->
        <div>
            <x-card title="Actions" variant="colored" color="gray" icon="fas fa-tools">
                <div class="space-y-3">
                    <a href="{{ route('type-annonces.edit', $typeAnnonce) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </a>
                    <form action="{{ route('type-annonces.destroy', $typeAnnonce) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </form>
                    <a href="{{ route('type-annonces.index') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection



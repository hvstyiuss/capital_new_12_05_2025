@extends('layouts.app')

@section('title', 'Détails de l\'Annonce')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Content -->
    <div class="mb-8">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/20 p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-bullhorn text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent flex items-center gap-2">
                            @if($annonce->typeAnnonce)
                                <span class="inline-block w-6 h-6 rounded-full" style="background-color: {{ $annonce->typeAnnonce->couleur ?: '#007bff' }}"></span>
                                {{ $annonce->typeAnnonce->nom }}
                            @else
                                Annonce
                            @endif
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 text-lg mt-2">Détails de l'annonce</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('annonces.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <a href="{{ route('annonces.edit', $annonce) }}" class="px-6 py-3 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Annonce Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Image Section -->
        @if($annonce->image)
        <div class="w-full h-64 overflow-hidden">
            <img src="{{ Storage::url($annonce->image) }}" alt="Annonce image" class="w-full h-full object-cover">
        </div>
        @endif

        <!-- Content Section -->
        <div class="p-8">
            <!-- Meta Information -->
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-user"></i>
                        <span class="font-semibold">{{ $annonce->user->name ?? 'Utilisateur' }}</span>
                    </div>
                    @if($annonce->entites->count() > 0)
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-building"></i>
                        <span>{{ $annonce->entites->pluck('nom')->join(', ') }}</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="far fa-clock"></i>
                        <span>{{ $annonce->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
                <div>
                    @if($annonce->statut === 'active')
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-pause-circle"></i> Inactive
                        </span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div class="prose max-w-none dark:prose-invert">
                <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                    {{ $annonce->content }}
                </div>
            </div>

            <!-- Admin Actions -->
            @if(auth()->check() && auth()->user()->hasRole('admin'))
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-4">
                <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection


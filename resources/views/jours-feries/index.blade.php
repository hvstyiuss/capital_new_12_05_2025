@extends('layouts.app')

@section('title', 'Jours Fériés')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            
            <div>
                <h1 class="text-4xl font-bold bg-clip-text">
                    Jours Fériés
                </h1>
                <p class="text-gray-600 text-lg mt-2">Calendrier des jours fériés</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20 mb-6">
        <form method="GET" action="{{ route('jours-feries.index') }}" class="row align-items-end">
            <div class="col-md-4 mb-3">
                <label for="year" class="form-label fw-semibold">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Année
                </label>
                <select name="year" id="year" class="form-select">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="search" class="form-label fw-semibold">
                    <i class="fas fa-search me-2 text-primary"></i>Rechercher
                </label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Rechercher par nom..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
            </div>
            @if(request()->has('search') || request()->has('year'))
                <div class="col-12">
                    <a href="{{ route('jours-feries.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Jours Fériés List -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20">
        <div class="p-6">
            @if($joursFeries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="fw-semibold">
                                    <i class="fas fa-calendar-day me-2 text-primary"></i>Date
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-tag me-2 text-primary"></i>Nom
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-list me-2 text-primary"></i>Type
                                </th>
                                <th class="fw-semibold">
                                    <i class="fas fa-clock me-2 text-primary"></i>Jour de la semaine
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($joursFeries as $jourFerie)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            {{ $jourFerie->date->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">{{ $jourFerie->name }}</td>
                                    <td>
                                        @if($jourFerie->typeJoursFerie)
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $jourFerie->typeJoursFerie->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ \Carbon\Carbon::parse($jourFerie->date)->locale('fr')->dayName }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        Total: <strong>{{ $joursFeries->count() }}</strong> jour(s) férié(s) en {{ $currentYear }}
                    </p>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times text-gray-400" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-gray-600 mb-2">Aucun jour férié trouvé</h3>
                    <p class="text-gray-500">
                        @if(request()->has('search') || request()->has('year'))
                            Aucun jour férié ne correspond à vos critères de recherche.
                        @else
                            Aucun jour férié n'est enregistré pour l'année {{ $currentYear }}.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


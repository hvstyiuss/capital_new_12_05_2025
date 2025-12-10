@extends('layouts.app')

@section('title', 'Préparer la Période - ' . $periode->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Préparer la Période {{ $periode->name }}</h1>
            <p class="text-muted mb-0">{{ $entite->name }}</p>
        </div>
        <a href="{{ route('deplacements.by-entity', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Agents Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-users me-2"></i>Agents et Jours Disponibles
                </h5>
                <div>
                    <a href="{{ route('deplacements.download-excel', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Télécharger Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3">Agent</th>
                            <th class="px-3 py-3">PPR</th>
                            @foreach($months as $month)
                                <th class="px-3 py-3 text-center">
                                    {{ \Carbon\Carbon::create($currentYear, $month, 1)->locale('fr')->monthName }}
                                </th>
                            @endforeach
                            <th class="px-3 py-3">Nombre de Jours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agentsData as $agentData)
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="fw-semibold">{{ $agentData['user']->fname }} {{ $agentData['user']->lname }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-secondary">{{ $agentData['user']->ppr }}</span>
                                </td>
                                @foreach($months as $month)
                                    <td class="px-3 py-3 text-center">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">{{ $agentData['months'][$month]['available'] ?? 0 }}</span>
                                            <small class="text-muted">
                                                Congés: {{ $agentData['months'][$month]['conges'] ?? 0 }} | 
                                                Fériés: {{ $agentData['months'][$month]['jours_feries'] ?? 0 }} | 
                                                Weekends: {{ $agentData['months'][$month]['weekends'] ?? 0 }}
                                            </small>
                                        </div>
                                    </td>
                                @endforeach
                                <td class="px-3 py-3">
                                    <span class="badge bg-info">{{ $agentData['max_jours'] }}</span>
                                </td>
                            </tr>
                            @if($agentData['conges']->count() > 0)
                                <tr class="bg-light">
                                    <td colspan="{{ count($months) + 3 }}" class="px-3 py-2">
                                        <small class="text-muted">
                                            <strong>Congés:</strong>
                                            @foreach($agentData['conges'] as $demande)
                                                @php
                                                    $conge = $demande->demandeConge;
                                                @endphp
                                                @if($conge)
                                                    {{ $conge->date_debut->format('d/m/Y') }} - {{ $conge->date_fin->format('d/m/Y') }} ({{ $conge->nbr_jours_demandes }}j)
                                                    @if(!$loop->last), @endif
                                                @endif
                                            @endforeach
                                        </small>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-end">
                <a href="{{ route('deplacements.start-process', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="btn btn-primary">
                    <i class="fas fa-play me-2"></i>Démarrer le processus
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


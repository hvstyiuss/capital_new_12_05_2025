@extends('layouts.app')

@section('title', 'Demandes de mes Agents')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2"></i>
                Demandes de mes Agents
            </h1>
            <div>
                <a href="{{ route('hr.leaves.annuel') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hr.leaves.agents') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="statut" class="form-label mb-1">Statut</label>
                    <select class="form-select" id="statut" name="statut" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('statut') == 'approved' ? 'selected' : '' }}>Validé</option>
                        <option value="rejected" {{ request('statut') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        <option value="cancelled" {{ request('statut') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label mb-1">Année</label>
                    <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="month" class="form-label mb-1">Mois</label>
                    <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                        <option value="">Tous les mois</option>
                        @php
                            $months = [
                                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('hr.leaves.agents') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Demandes Cards View -->
    <div class="row g-3">
        @forelse($demandes as $item)
        <div class="col-12">
            <div class="card shadow-sm border-left-4" style="border-left-color: {{ $item->statut == 'pending' ? '#ffc107' : ($item->statut == 'approved' ? '#28a745' : '#dc3545') }};">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <!-- Left Section: Basic Info -->
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-secondary me-2">#{{ $item->id }}</span>
                                <h6 class="mb-0">
                                    <a href="{{ route('hr.leaves.user-info', $item->ppr) }}" 
                                       class="text-decoration-none text-success fw-bold">
                                        {{ $item->user_name }}
                                    </a>
                                </h6>
                            </div>
                            <div class="text-muted small">
                                <div><i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->date_depot)->format('d/m/Y H:i') }}</div>
                                <div class="mt-1"><i class="fas fa-tag me-1"></i> {{ $item->type_demande }}</div>
                            </div>
                        </div>

                        <!-- Middle Section: Avis de Départ -->
                        <div class="col-md-4 border-start border-end px-3">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-plane-departure me-1" style="color: rgb(99, 163, 114);"></i>
                                    <strong>Avis de Départ</strong>
                                </small>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @if($item->nbr_jours)
                                        <span class="badge bg-primary">{{ $item->nbr_jours }}j</span>
                                    @endif
                                    @if($item->date_debut)
                                        <small><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->date_debut)->format('d/m/Y') }}</small>
                                    @endif
                                    @if($item->date_retour)
                                        <small><i class="fas fa-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($item->date_retour)->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($item->avis_depart_statut_label)
                                    @php
                                        $statut = $item->avis_depart_statut ?? 'pending';
                                        $badgeClass = match($statut) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            'cancelled' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $item->avis_depart_statut_label }}</span>
                                @endif
                                @if($item->avis_depart_statut == 'approved' && isset($item->avis_depart_id) && $item->avis_depart_id && isset($item->avis_depart_pdf_path) && $item->avis_depart_pdf_path)
                                    <a href="{{ route('hr.leaves.download-avis-depart-pdf', $item->avis_depart_id) }}" 
                                       class="text-danger" 
                                       target="_blank"
                                       title="Avis de Départ PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                                @if($item->avis_depart_statut == 'pending' && isset($item->avis_depart_id) && $item->avis_depart_id)
                                    <form action="{{ route('hr.leaves.validate-avis-depart', $item->avis_depart_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir valider cet avis de départ?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> Valider
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Right Section: Avis de Retour -->
                        <div class="col-md-3 px-3">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-plane-arrival me-1" style="color: rgb(238, 181, 135);"></i>
                                    <strong>Avis de Retour</strong>
                                </small>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @if($item->nbr_jours_consommes)
                                        <span class="badge bg-info">{{ $item->nbr_jours_consommes }}j</span>
                                    @endif
                                    @if($item->date_retour_declaree)
                                        <small><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->date_retour_declaree)->format('d/m/Y') }}</small>
                                    @endif
                                    @if($item->date_retour_effectif)
                                        <small><i class="fas fa-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($item->date_retour_effectif)->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->avis_retour_statut_label)
                                        @php
                                            $statut = $item->avis_retour_statut ?? 'pending';
                                            $badgeClass = match($statut) {
                                                'approved' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                'cancelled' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $item->avis_retour_statut_label }}</span>
                                    @endif
                                    @if($item->avis_retour_statut == 'approved' && isset($item->avis_retour_id) && $item->avis_retour_id)
                                        @if(isset($item->avis_retour_pdf_path) && $item->avis_retour_pdf_path)
                                            <a href="{{ route('hr.leaves.download-avis-retour-pdf', $item->avis_retour_id) }}" 
                                               class="text-success" 
                                               target="_blank"
                                               title="Avis de Retour PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @elseif(isset($item->explanation_pdf_path) && $item->explanation_pdf_path)
                                            <a href="{{ route('hr.leaves.download-explanation-pdf', $item->avis_retour_id) }}" 
                                               class="text-danger" 
                                               target="_blank"
                                               title="Note d'Explication PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                @if($item->avis_retour_statut == 'pending' && isset($item->avis_retour_id) && $item->avis_retour_id)
                                    <form action="{{ route('hr.leaves.validate-avis-retour', $item->avis_retour_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir valider cet avis de retour?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> Valider
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Actions Section -->
                        <div class="col-md-2 text-end">
                            <div class="d-flex flex-column gap-2 align-items-end">
                                @php
                                    $consumptionExceeds = isset($item->consumption_exceeds) && $item->consumption_exceeds;
                                @endphp
                                <a href="{{ route('hr.leaves.show', $item->id) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Voir les détails">
                                    <i class="fas fa-eye me-1"></i> Détails
                                </a>
                                @if($item->statut == 'pending')
                                    <a href="{{ route('hr.leaves.show', $item->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-cog me-1"></i> Traiter
                                    </a>
                                @else
                                    @if($item->statut == 'approved' && $item->avis_retour_statut == 'approved')
                                        @if($consumptionExceeds && $item->avis_retour_id)
                                            <a href="{{ route('hr.leaves.download-explanation-pdf', $item->avis_retour_id) }}" 
                                               class="btn btn-sm btn-outline-danger" 
                                               target="_blank"
                                               title="Note d'explication">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </a>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    @endif
                                @endif
                                @if($consumptionExceeds)
                                    <i class="fas fa-question-circle text-danger" title="Consommation supérieure à la date de retour déclarée"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Aucune demande trouvée</p>
                    @if(request('statut') || request('year'))
                        <a href="{{ route('hr.leaves.agents') }}" class="btn btn-sm btn-outline-primary mt-3">
                            <i class="fas fa-redo me-2"></i>Réinitialiser les filtres
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Old Table (Hidden by default, can be toggled) -->
    <div class="card shadow-sm d-none" id="tableView">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-bordered table-hover mb-0 no-row-click" style="font-size: 0.9rem; width: 100%;">
                    <thead>
                        <!-- Primary Headers -->
                        <tr>
                            <th colspan="4" class="text-center align-middle" style="background-color: #e9ecef; color: #495057; font-weight: 600; padding: 12px;">
                                Données Demande
                            </th>
                            <th colspan="5" class="text-center align-middle" style="background-color:rgb(99, 163, 114); color: #495057; font-weight: 600; padding: 12px;">
                                Avis de Départ
                            </th>
                            <th colspan="6" class="text-center align-middle" style="background-color:rgb(238, 181, 135); color: #495057; font-weight: 600; padding: 12px;">
                                Avis de Retour
                            </th>
                            <th colspan="4" class="text-center align-middle" style="background-color: #e9ecef; color: #495057; font-weight: 600; padding: 12px;">
                                Traitement
                            </th>
                        </tr>
                        <!-- Secondary Headers -->
                        <tr>
                            <!-- Données Demande -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 60px; padding: 10px; position: relative;">
                                Nº
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 180px; padding: 10px; position: relative;">
                                Collaborateur
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px; position: relative;">
                                Type Demande
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 150px; padding: 10px; position: relative;">
                                Date Depot
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <!-- Avis de Départ -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px; position: relative;">
                                Nbr Jours Demandés
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px; position: relative;">
                                Date de Départ
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px; position: relative;">
                                Date de Retour
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px; position: relative;">
                                Statut
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 80px; padding: 10px;">
                                PDF
                            </th>
                            <!-- Avis de Retour -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 150px; padding: 10px; position: relative;">
                                Date Depot
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px; position: relative;">
                                Nbr Jours Consommés
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px; position: relative;">
                                Date de Retour Déclarée
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px; position: relative;">
                                Date Retour Effectif
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px; position: relative;">
                                Statut
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 80px; padding: 10px;">
                                PDF
                            </th>
                            <!-- Traitement -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 60px; padding: 10px;">
                                <i class="fas fa-eye" style="font-size: 1rem;"></i>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 60px; padding: 10px;">
                                <i class="fas fa-question-circle" style="font-size: 1rem;"></i>
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px;">
                                <!-- Button column header -->
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 80px; padding: 10px;">
                                <!-- Status column header -->
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $item)
                            <tr>
                                <!-- Données Demande -->
                                <td class="text-center align-middle" style="padding: 10px;">{{ $item->id }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    <a href="{{ route('hr.leaves.user-info', $item->ppr) }}" 
                                       class="text-decoration-none" 
                                       style="color: #28a745; font-weight: 500; cursor: pointer;">
                                        {{ $item->user_name }}
                                    </a>
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">{{ $item->type_demande }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    {{ \Carbon\Carbon::parse($item->date_depot)->format('Y-m-d H:i') }}
                                </td>
                                <!-- Avis de Départ -->
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->nbr_jours)
                                        {{ $item->nbr_jours }}j
                                    @else
                                        <span style="color: #6c757d;">(?)j</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->date_debut)
                                        {{ \Carbon\Carbon::parse($item->date_debut)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->date_retour)
                                        {{ \Carbon\Carbon::parse($item->date_retour)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                    @if($item->avis_depart_statut_label)
                                        @php
                                            $statut = $item->avis_depart_statut ?? 'pending';
                                            $badgeClass = match($statut) {
                                                'approved' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                'cancelled' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}" style="pointer-events: none;">{{ $item->avis_depart_statut_label }}</span>
                                    @else
                                        <span style="color: #6c757d; pointer-events: none;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                    @if($item->avis_depart_statut == 'approved' && isset($item->avis_depart_id) && $item->avis_depart_id && isset($item->avis_depart_pdf_path) && $item->avis_depart_pdf_path)
                                        <a href="{{ route('hr.leaves.download-avis-depart-pdf', $item->avis_depart_id) }}" 
                                           class="text-danger" 
                                           target="_blank"
                                           title="Avis de Départ PDF"
                                           onclick="event.stopPropagation();">
                                            <i class="fas fa-file-pdf" style="font-size: 1.2rem;"></i>
                                        </a>
                                    @elseif($item->avis_depart_statut == 'pending' && isset($item->avis_depart_id) && $item->avis_depart_id)
                                        <form action="{{ route('hr.leaves.validate-avis-depart', $item->avis_depart_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir valider cet avis de départ?');" onclick="event.stopPropagation();">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="event.stopPropagation();">
                                                <i class="fas fa-check me-1"></i> Valider
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: #6c757d; pointer-events: none;">-</span>
                                    @endif
                                </td>
                                <!-- Avis de Retour -->
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->avis_retour_date_depot)
                                        {{ \Carbon\Carbon::parse($item->avis_retour_date_depot)->format('Y-m-d H:i') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->nbr_jours_consommes)
                                        {{ $item->nbr_jours_consommes }}j
                                    @else
                                        <span style="color: #6c757d;">(?)j</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->date_retour_declaree)
                                        {{ \Carbon\Carbon::parse($item->date_retour_declaree)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->date_retour_effectif)
                                        {{ \Carbon\Carbon::parse($item->date_retour_effectif)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                    @if($item->avis_retour_statut_label)
                                        @php
                                            $statut = $item->avis_retour_statut ?? 'pending';
                                            $badgeClass = match($statut) {
                                                'approved' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                'cancelled' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}" style="pointer-events: none;">{{ $item->avis_retour_statut_label }}</span>
                                    @else
                                        <span style="color: #6c757d; pointer-events: none;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                    @if($item->avis_retour_statut == 'approved' && isset($item->avis_retour_id) && $item->avis_retour_id)
                                        @if(isset($item->avis_retour_pdf_path) && $item->avis_retour_pdf_path)
                                            <a href="{{ route('hr.leaves.view-avis-retour-pdf', $item->avis_retour_id) }}" 
                                               class="text-success" 
                                               title="Voir Avis de Retour PDF avec Solde"
                                               onclick="event.stopPropagation();">
                                                <i class="fas fa-file-pdf" style="font-size: 1.2rem;"></i>
                                            </a>
                                        @elseif(isset($item->explanation_pdf_path) && $item->explanation_pdf_path)
                                            <a href="{{ route('hr.leaves.download-explanation-pdf', $item->avis_retour_id) }}" 
                                               class="text-danger" 
                                               target="_blank"
                                               title="Note d'Explication PDF"
                                               onclick="event.stopPropagation();">
                                                <i class="fas fa-file-pdf" style="font-size: 1.2rem;"></i>
                                            </a>
                                        @else
                                            <span style="color: #6c757d; pointer-events: none;">-</span>
                                        @endif
                                    @else
                                        <span style="color: #6c757d; pointer-events: none;">-</span>
                                    @endif
                                </td>
                                <!-- Traitement -->
                                <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                    <a href="{{ route('hr.leaves.show', $item->id) }}" class="text-primary" title="Voir les détails" onclick="event.stopPropagation();">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @php
                                        // Check if consumption exceeds declared return date
                                        // Red if consumption exceeds (PDF needed), gray otherwise
                                        $consumptionExceeds = isset($item->consumption_exceeds) && $item->consumption_exceeds;
                                        $iconColor = $consumptionExceeds ? '#dc3545' : '#6c757d';
                                    @endphp
                                    <i class="fas fa-question-circle" style="color: {{ $iconColor }};"></i>
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if($item->statut == 'pending')
                                        <button type="button" class="btn btn-sm btn-secondary" style="background-color: #6c757d; border-color: #6c757d; padding: 4px 12px; font-size: 0.85rem;" 
                                                onclick="window.location.href='{{ route('hr.leaves.show', $item->id) }}'">
                                            Traiter
                                        </button>
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                    @if($item->statut == 'approved' && $item->avis_retour_statut == 'approved')
                                        @if(isset($item->consumption_exceeds) && $item->consumption_exceeds && $item->avis_retour_id)
                                            <a href="{{ route('hr.leaves.download-explanation-pdf', $item->avis_retour_id) }}" 
                                               class="btn btn-sm btn-outline-danger" 
                                               target="_blank"
                                               title="Note d'explication - Consommation supérieure à la date de retour déclarée"
                                               onclick="event.stopPropagation();">
                                                <i class="fas fa-file-pdf me-1"></i>
                                                PDF
                                            </a>
                                        @else
                                            <span style="color: #28a745; font-weight: 500; pointer-events: none; cursor: default;">OK</span>
                                        @endif
                                    @else
                                        <span style="color: #6c757d; pointer-events: none;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucune demande trouvée</p>
                                        @if(request('statut') || request('year'))
                                            <a href="{{ route('hr.leaves.agents') }}" class="btn btn-sm btn-outline-primary mt-3">
                                                <i class="fas fa-redo me-2"></i>Réinitialiser les filtres
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($demandes->hasPages())
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <p class="mb-0 text-muted">
                            Affichage de <strong>{{ $demandes->firstItem() }}</strong> à <strong>{{ $demandes->lastItem() }}</strong> sur <strong>{{ $demandes->total() }}</strong> demande(s)
                        </p>
                    </div>
                    <div>
                        {{ $demandes->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    // Prevent loading state ("Envoi en cours...") for approve and reject forms
    // Run immediately to catch forms before global handlers attach
    function preventLoadingState() {
        const approveRejectForms = document.querySelectorAll('form[action*="approve-chef"], form[action*="reject-chef"]');
        
        approveRejectForms.forEach(function(form) {
            // Mark form to skip loading state
            form.setAttribute('data-skip-loading', 'true');
            
            // Intercept form submit before global handlers
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Mark button to prevent loading state
                    submitBtn.setAttribute('data-skip-loading', 'true');
                    submitBtn.setAttribute('data-submitting', 'true'); // Prevent global handler
                    
                    // Clean up any loading state that might have been added
                    const cleanup = function() {
                        const btnText = submitBtn.querySelector('.btn-text');
                        if (btnText && (btnText.textContent.includes('Envoi en cours') || btnText.textContent.includes('Envoi...'))) {
                            btnText.remove();
                        }
                        const spinner = submitBtn.querySelector('.loading-spinner');
                        if (spinner) {
                            spinner.remove();
                        }
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    };
                    
                    // Clean up immediately and after a short delay
                    cleanup();
                    setTimeout(cleanup, 50);
                    setTimeout(cleanup, 200);
                }
            }, true); // Capture phase - runs before other handlers
        });
    }
    
    // Run immediately if DOM is ready, otherwise wait
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', preventLoadingState);
    } else {
        preventLoadingState();
    }
    
    // Also run after a short delay to catch any late-loading scripts
    setTimeout(preventLoadingState, 100);
})();
</script>
@endpush

@endsection

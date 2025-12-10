@extends('layouts.app')

@section('title', 'Suivi des Demandes de Congés')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Suivi des Demandes de Congés
        </h1>
    </div>

    <!-- Alert Message - Fixed Position -->
    <div class="alert alert-warning alert-dismissible fade show mb-4" id="leaveInfoAlert" style="position: sticky; top: 0; z-index: 1000;">
        <div class="d-flex align-items-start">
            <i class="fas fa-exclamation-triangle me-3 mt-1" style="font-size: 1.2rem;"></i>
            <div class="flex-grow-1">
                <p class="mb-2">
                     Après avoir soumis votre demande de congé, vous pouvez l'annuler uniquement si elle est encore en attente d'approbation. Une fois votre demande approuvée, vous devez soumettre un avis de départ le jour de votre départ, puis un avis de retour le jour de votre retour effectif.
                </p>
                <p class="mb-0">
                     بعد تقديم طلب الإجازة، يمكنكم إلغاء الطلب فقط إذا كان لا يزال في انتظار الموافقة. بمجرد الموافقة على طلبكم، يجب عليكم تقديم إشعار المغادرة في يوم مغادرتكم، ثم إشعار العودة في يوم عودتكم الفعلية.
                </p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="year" class="form-label mb-1">Année</label>
                    <select class="form-select" id="year">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="per_page" class="form-label mb-1">Afficher</label>
                    <select class="form-select" id="per_page">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 lignes par page</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 lignes par page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 lignes par page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 lignes par page</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label mb-1">Rechercher :</label>
                    <input type="text" class="form-control" id="search" placeholder="Rechercher..." value="{{ $search }}">
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle Button -->
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleViewBtn" onclick="toggleView()">
            <i class="fas fa-table me-1"></i> Vue Tableau
        </button>
    </div>

    <!-- Demandes Cards View -->
    <div class="row g-3" id="demandesCardsContainer">
        @forelse($items as $index => $demande)
        <div class="col-12">
            <div class="card shadow-sm border-left-4" style="border-left-color: {{ isset($demande['statut']) && (strtolower($demande['statut']) == 'rejeté' || strtolower($demande['statut']) == 'rejected') ? '#dc3545' : (isset($demande['statut']) && (strtolower($demande['statut']) == 'approuvé' || strtolower($demande['statut']) == 'approved' || strtolower($demande['statut']) == 'validé') ? '#28a745' : '#ffc107') }};">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <!-- Left Section: Basic Info -->
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-secondary me-2">#{{ $demande['id'] }}</span>
                                <h6 class="mb-0 fw-bold text-gray-800">{{ $demande['type'] }}</h6>
                            </div>
                            <div class="text-muted small">
                                <div><i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($demande['date_depot'])->format('d/m/Y H:i') }}</div>
                                <div class="mt-1"><i class="fas fa-calendar-day me-1"></i> {{ $demande['nbr_jours'] }}j</div>
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
                                    @if($demande['date_depart'])
                                        <small><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($demande['date_depart'])->format('d/m/Y') }}</small>
                                    @endif
                                    @if($demande['date_retour'])
                                        <small><i class="fas fa-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($demande['date_retour'])->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if(isset($demande['avis_depart']['statut']))
                                    @php
                                        $statut = $demande['avis_depart']['statut'];
                                        $badgeClass = match($statut) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            'cancelled' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                        $statutMap = [
                                            'pending' => 'En attente',
                                            'approved' => 'Validé',
                                            'rejected' => 'Rejeté',
                                            'cancelled' => 'Annulé',
                                        ];
                                        $statutLabel = $statutMap[$statut] ?? $statut;
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $statutLabel }}</span>
                                @endif
                                @if(isset($demande['avis_depart']['statut']) && $demande['avis_depart']['statut'] == 'approved' && isset($demande['avis_depart']['id']) && $demande['avis_depart']['id'])
                                    @if(isset($demande['avis_depart']['pdf_path']) && $demande['avis_depart']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-depart-pdf', $demande['avis_depart']['id']) }}" 
                                           class="text-danger" 
                                           target="_blank"
                                           title="Avis de Départ PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
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
                                    @if(isset($demande['avis_retour']['nbr_jours_consommes']))
                                        <span class="badge bg-info">{{ $demande['avis_retour']['nbr_jours_consommes'] }}j</span>
                                    @endif
                                    @if(isset($demande['avis_retour']['date_retour_declaree']))
                                        <small><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($demande['avis_retour']['date_retour_declaree'])->format('d/m/Y') }}</small>
                                    @endif
                                    @if(isset($demande['avis_retour']['date_retour_effectif']))
                                        <small><i class="fas fa-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($demande['avis_retour']['date_retour_effectif'])->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if(isset($demande['avis_retour']['statut_raw']))
                                    @php
                                        $statut = $demande['avis_retour']['statut_raw'];
                                        $badgeClass = match($statut) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            'cancelled' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $demande['avis_retour']['statut'] ?? '-' }}</span>
                                @endif
                                @if(isset($demande['avis_retour']['statut_raw']) && $demande['avis_retour']['statut_raw'] == 'approved' && isset($demande['avis_retour']['id']) && $demande['avis_retour']['id'])
                                    @if(isset($demande['avis_retour']['pdf_path']) && $demande['avis_retour']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-retour-pdf', $demande['avis_retour']['id']) }}" 
                                           class="text-success" 
                                           target="_blank"
                                           title="Avis de Retour PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @elseif(isset($demande['avis_retour']['explanation_pdf_path']) && $demande['avis_retour']['explanation_pdf_path'])
                                        <a href="{{ route('hr.leaves.download-explanation-pdf', $demande['avis_retour']['id']) }}" 
                                           class="text-danger" 
                                           target="_blank"
                                           title="Note d'Explication PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Actions Section -->
                        <div class="col-md-2 text-end">
                            <div class="d-flex flex-column gap-2 align-items-end">
                                @if(isset($demande['avis_depart']['statut']) && $demande['avis_depart']['statut'] == 'approved' && isset($demande['avis_depart']['id']) && $demande['avis_depart']['id'])
                                    @if(isset($demande['avis_depart']['pdf_path']) && $demande['avis_depart']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-depart-pdf', $demande['avis_depart']['id']) }}" 
                                           class="btn btn-sm btn-outline-danger" 
                                           target="_blank"
                                           title="Avis de Départ PDF">
                                            <i class="fas fa-file-pdf me-1"></i> PDF Départ
                                        </a>
                                    @endif
                                @endif
                                @if(isset($demande['avis_retour']['statut_raw']) && $demande['avis_retour']['statut_raw'] == 'approved' && isset($demande['avis_retour']['id']) && $demande['avis_retour']['id'])
                                    @if(isset($demande['avis_retour']['pdf_path']) && $demande['avis_retour']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-retour-pdf', $demande['avis_retour']['id']) }}" 
                                           class="btn btn-sm btn-outline-success" 
                                           target="_blank"
                                           title="Avis de Retour PDF">
                                            <i class="fas fa-file-pdf me-1"></i> PDF Retour
                                        </a>
                                    @elseif(isset($demande['avis_retour']['explanation_pdf_path']) && $demande['avis_retour']['explanation_pdf_path'])
                                        <a href="{{ route('hr.leaves.download-explanation-pdf', $demande['avis_retour']['id']) }}" 
                                           class="btn btn-sm btn-outline-danger" 
                                           target="_blank"
                                           title="Note d'Explication PDF">
                                            <i class="fas fa-file-pdf me-1"></i> PDF Explication
                                        </a>
                                    @endif
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
                    <p class="text-muted mb-0">Aucune demande de congé trouvée</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Old Table (Hidden by default, can be toggled) -->
    <div class="card shadow-sm d-none" id="tableView">
        <div id="demandesTableContainer">
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
                            <th colspan="2" class="text-center align-middle" style="background-color: #e9ecef; color: #495057; font-weight: 600; padding: 12px;">
                                Impression
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
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px; position: relative;">
                                Nbr Jours Demandés
                                <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;">
                                    <i class="fas fa-sort-up" style="opacity: 0.5;"></i><br>
                                    <i class="fas fa-sort-down" style="opacity: 0.5; margin-top: -5px;"></i>
                                </span>
                            </th>
                            <!-- Avis de Départ -->
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
                            <!-- Impression -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 80px; padding: 10px;">
                                Avis de Départ
                            </th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 80px; padding: 10px;">
                                Avis de Retour
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $demande)
                        <tr>
                            <!-- Données Demande -->
                            <td class="text-center align-middle" style="padding: 10px;">{{ $demande['id'] }}</td>
                            <td class="text-center align-middle" style="padding: 10px;">{{ $demande['type'] }}</td>
                            <td class="text-center align-middle" style="padding: 10px;">
                                {{ \Carbon\Carbon::parse($demande['date_depot'])->format('Y-m-d H:i') }}
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;">{{ $demande['nbr_jours'] }}j</td>
                            <!-- Avis de Départ -->
                            <td class="text-center align-middle" style="padding: 10px;">
                                @if($demande['date_depart'])
                                    {{ \Carbon\Carbon::parse($demande['date_depart'])->format('Y-m-d') }}
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;">
                                @if($demande['date_retour'])
                                    {{ \Carbon\Carbon::parse($demande['date_retour'])->format('Y-m-d') }}
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                @if(isset($demande['avis_depart']['statut']))
                                    @php
                                        $statut = $demande['avis_depart']['statut'];
                                        $badgeClass = match($statut) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            'cancelled' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                        $statutMap = [
                                            'pending' => 'En attente',
                                            'approved' => 'Validé',
                                            'rejected' => 'Rejeté',
                                            'cancelled' => 'Annulé',
                                        ];
                                        $statutLabel = $statutMap[$statut] ?? $statut;
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="pointer-events: none;">{{ $statutLabel }}</span>
                                @else
                                    <span style="color: #6c757d; pointer-events: none;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                @if(isset($demande['avis_depart']['statut']) && $demande['avis_depart']['statut'] == 'approved' && isset($demande['avis_depart']['id']) && $demande['avis_depart']['id'])
                                    @if(isset($demande['avis_depart']['pdf_path']) && $demande['avis_depart']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-depart-pdf', $demande['avis_depart']['id']) }}" 
                                           class="text-danger" 
                                           target="_blank"
                                           title="Avis de Départ PDF"
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
                            <!-- Avis de Retour -->
                            <td class="text-center align-middle" style="padding: 10px;">
                                @if(isset($demande['avis_retour']['date_depot']))
                                    {{ \Carbon\Carbon::parse($demande['avis_retour']['date_depot'])->format('Y-m-d H:i') }}
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;">
                                @if(isset($demande['avis_retour']['nbr_jours_consommes']))
                                    {{ $demande['avis_retour']['nbr_jours_consommes'] }}j
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;">
                                @if(isset($demande['avis_retour']['date_retour_declaree']))
                                    {{ \Carbon\Carbon::parse($demande['avis_retour']['date_retour_declaree'])->format('Y-m-d') }}
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;">
                                @if(isset($demande['avis_retour']['date_retour_effectif']))
                                    {{ \Carbon\Carbon::parse($demande['avis_retour']['date_retour_effectif'])->format('Y-m-d') }}
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                @if(isset($demande['avis_retour']['statut_raw']))
                                    @php
                                        $statut = $demande['avis_retour']['statut_raw'];
                                        $badgeClass = match($statut) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            'cancelled' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="pointer-events: none;">{{ $demande['avis_retour']['statut'] ?? '-' }}</span>
                                @else
                                    <span style="color: #6c757d; pointer-events: none;">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                @if(isset($demande['avis_retour']['statut_raw']) && $demande['avis_retour']['statut_raw'] == 'approved' && isset($demande['avis_retour']['id']) && $demande['avis_retour']['id'])
                                    @if(isset($demande['avis_retour']['pdf_path']) && $demande['avis_retour']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-retour-pdf', $demande['avis_retour']['id']) }}" 
                                           class="text-success" 
                                           target="_blank"
                                           title="Avis de Retour PDF"
                                           onclick="event.stopPropagation();">
                                            <i class="fas fa-file-pdf" style="font-size: 1.2rem;"></i>
                                        </a>
                                    @elseif(isset($demande['avis_retour']['explanation_pdf_path']) && $demande['avis_retour']['explanation_pdf_path'])
                                        <a href="{{ route('hr.leaves.download-explanation-pdf', $demande['avis_retour']['id']) }}" 
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
                            <!-- Impression -->
                            <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                @if(isset($demande['avis_depart']['statut']) && $demande['avis_depart']['statut'] == 'approved' && isset($demande['avis_depart']['id']) && $demande['avis_depart']['id'])
                                    @if(isset($demande['avis_depart']['pdf_path']) && $demande['avis_depart']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-depart-pdf', $demande['avis_depart']['id']) }}" 
                                           class="text-danger" 
                                           target="_blank"
                                           title="Avis de Départ PDF"
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
                            <td class="text-center align-middle" style="padding: 10px;" onclick="event.stopPropagation();">
                                @if(isset($demande['avis_retour']['statut_raw']) && $demande['avis_retour']['statut_raw'] == 'approved' && isset($demande['avis_retour']['id']) && $demande['avis_retour']['id'])
                                    @if(isset($demande['avis_retour']['pdf_path']) && $demande['avis_retour']['pdf_path'])
                                        <a href="{{ route('hr.leaves.download-avis-retour-pdf', $demande['avis_retour']['id']) }}" 
                                           class="text-success" 
                                           target="_blank"
                                           title="Avis de Retour PDF"
                                           onclick="event.stopPropagation();">
                                            <i class="fas fa-file-pdf" style="font-size: 1.2rem;"></i>
                                        </a>
                                    @elseif(isset($demande['avis_retour']['explanation_pdf_path']) && $demande['avis_retour']['explanation_pdf_path'])
                                        <a href="{{ route('hr.leaves.download-explanation-pdf', $demande['avis_retour']['id']) }}" 
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="17" class="text-center py-4">
                                <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Aucune demande de congé trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    <!-- Pagination Footer -->
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <div>
            <span class="text-muted">Page {{ $currentPage }} sur {{ $lastPage }}</span>
        </div>
        <div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('leaves.tracking', ['page' => $currentPage - 1, 'year' => $year, 'per_page' => $perPage, 'search' => $search]) }}">
                            Précédent
                        </a>
                    </li>
                    @for($i = 1; $i <= $lastPage; $i++)
                        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                            <a class="page-link" href="{{ route('leaves.tracking', ['page' => $i, 'year' => $year, 'per_page' => $perPage, 'search' => $search]) }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor
                    <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('leaves.tracking', ['page' => $currentPage + 1, 'year' => $year, 'per_page' => $perPage, 'search' => $search]) }}">
                            Suivant
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-responsive {
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        position: relative;
        max-width: 100%;
    }

    .table-responsive::-webkit-scrollbar {
        height: 12px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .table {
        min-width: 1400px;
        width: 100%;
        table-layout: auto;
    }

    .table th {
        font-weight: 600;
        vertical-align: middle;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table thead th.bg-light {
        background-color: #f8f9fa !important;
    }

    .table thead th:first-child {
        position: sticky;
        left: 0;
        z-index: 11;
        background-color: #f8f9fa !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .table tbody td:first-child {
        position: sticky;
        left: 0;
        z-index: 9;
        background-color: white;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .table tbody tr:hover td:first-child {
        background-color: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover td:first-child {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    .alert-warning {
        background-color: #fff3cd;
        padding: 1rem;
        border-color: #ffc107;
        border-radius: 0.5rem;
        color: #856404;
    }

    #leaveInfoAlert {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Scroll indicator */
    .table-scroll-indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #007bff, #28a745);
        transition: width 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        z-index: 1000;
    }

    .table-responsive:hover .table-scroll-indicator {
        opacity: 1;
    }

    .border-left-4 {
        border-left-width: 4px !important;
    }
</style>
@endpush

@push('scripts')
<script>
function toggleView() {
    const cardsView = document.querySelector('.row.g-3');
    const tableView = document.getElementById('tableView');
    const toggleBtn = document.getElementById('toggleViewBtn');
    
    if (cardsView && tableView && toggleBtn) {
        if (cardsView.classList.contains('d-none')) {
            // Switch to cards view
            cardsView.classList.remove('d-none');
            tableView.classList.add('d-none');
            toggleBtn.innerHTML = '<i class="fas fa-table me-1"></i> Vue Tableau';
        } else {
            // Switch to table view
            cardsView.classList.add('d-none');
            tableView.classList.remove('d-none');
            toggleBtn.innerHTML = '<i class="fas fa-th-large me-1"></i> Vue Cartes';
        }
    }
}
</script>
<script>
// Frontend filtering - store all demandes data
const allDemandesData = @json($allDemandesData ?? []);
let currentPage = 1;
let searchTimeout;

// Filter and paginate demandes on frontend
function filterAndPaginateDemandes() {
    const yearSelect = document.getElementById('year');
    const perPageSelect = document.getElementById('per_page');
    const searchInput = document.getElementById('search');
    
    const selectedYear = yearSelect ? parseInt(yearSelect.value) : new Date().getFullYear();
    const perPage = perPageSelect ? parseInt(perPageSelect.value) : 10;
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

    // Filter demandes
    let filteredDemandes = allDemandesData.filter(function(demande) {
        // Filter by year
        const demandeYear = new Date(demande.date_depot).getFullYear();
        if (demandeYear !== selectedYear) {
            return false;
        }

        // Filter by search term (search in statut, type, user_name)
        if (searchTerm) {
            const statut = (demande.statut || '').toLowerCase();
            const type = (demande.type || '').toLowerCase();
            const userName = (demande.user_name || '').toLowerCase();
            
            if (!statut.includes(searchTerm) && 
                !type.includes(searchTerm) && 
                !userName.includes(searchTerm)) {
                return false;
            }
        }

        return true;
    });

    // Calculate pagination
    const total = filteredDemandes.length;
    const totalPages = Math.ceil(total / perPage);
    const startIndex = (currentPage - 1) * perPage;
    const endIndex = startIndex + perPage;
    const paginatedDemandes = filteredDemandes.slice(startIndex, endIndex);

    // Render cards and table
    renderDemandes(paginatedDemandes, total, startIndex, endIndex, currentPage, totalPages);
}

// Render demandes (both cards and table)
function renderDemandes(demandes, total, startIndex, endIndex, currentPage, totalPages) {
    const cardsContainer = document.getElementById('demandesCardsContainer');
    const tableContainer = document.getElementById('demandesTableContainer');
    
    if (demandes.length === 0) {
        const emptyHtml = `
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Aucune demande de congé trouvée</p>
                    </div>
                </div>
            </div>
        `;
        if (cardsContainer) cardsContainer.innerHTML = emptyHtml;
        if (tableContainer) {
            tableContainer.innerHTML = `
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Aucune demande de congé trouvée</p>
                </div>
            `;
        }
        renderPagination(total, startIndex, endIndex, currentPage, totalPages);
        return;
    }

    // Render cards
    let cardsHtml = '';
    demandes.forEach(function(demande) {
        const dateDepot = new Date(demande.date_depot);
        const dateFormatted = dateDepot.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        
        const statutLower = (demande.statut || '').toLowerCase();
        const borderColor = (statutLower == 'rejeté' || statutLower == 'rejected') ? '#dc3545' : 
                          (statutLower == 'approuvé' || statutLower == 'approved' || statutLower == 'validé') ? '#28a745' : '#ffc107';
        
        const dateDepart = demande.date_depart ? new Date(demande.date_depart).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '';
        const dateRetour = demande.date_retour ? new Date(demande.date_retour).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '';
        
        // Status badge
        let statutBadge = '';
        if (statutLower == 'rejeté' || statutLower == 'rejected') {
            statutBadge = '<span class="badge bg-danger">Rejeté</span>';
        } else if (statutLower == 'approuvé' || statutLower == 'approved' || statutLower == 'validé') {
            statutBadge = '<span class="badge bg-success">Validé</span>';
        } else {
            statutBadge = '<span class="badge bg-warning text-dark">En attente</span>';
        }
        
        // Avis de départ info
        let avisDepartHtml = '';
        if (demande.avis_depart) {
            const avisDepartStatut = demande.avis_depart.statut || 'pending';
            const avisDepartStatutLower = avisDepartStatut.toLowerCase();
            let avisDepartBadge = '';
            if (avisDepartStatutLower == 'approved') {
                avisDepartBadge = '<span class="badge bg-success">Validé</span>';
            } else if (avisDepartStatutLower == 'rejected') {
                avisDepartBadge = '<span class="badge bg-danger">Rejeté</span>';
            } else {
                avisDepartBadge = '<span class="badge bg-warning text-dark">En attente</span>';
            }
            
            avisDepartHtml = `
                <div class="mb-2">
                    <small class="text-muted d-block mb-1">
                        <i class="fas fa-plane-departure me-1" style="color: rgb(99, 163, 114);"></i>
                        <strong>Avis de Départ</strong>
                    </small>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        ${dateDepart ? `<small><i class="fas fa-calendar me-1"></i>${dateDepart}</small>` : ''}
                        ${dateRetour ? `<small><i class="fas fa-calendar-check me-1"></i>${dateRetour}</small>` : ''}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    ${avisDepartBadge}
                </div>
            `;
        }
        
        // Avis de retour info
        let avisRetourHtml = '';
        if (demande.avis_retour) {
            const avisRetourStatut = demande.avis_retour.statut_raw || demande.avis_retour.statut || 'pending';
            const avisRetourStatutLower = avisRetourStatut.toLowerCase();
            let avisRetourBadge = '';
            if (avisRetourStatutLower == 'approved') {
                avisRetourBadge = '<span class="badge bg-success">Validé</span>';
            } else if (avisRetourStatutLower == 'rejected') {
                avisRetourBadge = '<span class="badge bg-danger">Rejeté</span>';
            } else {
                avisRetourBadge = '<span class="badge bg-warning text-dark">En attente</span>';
            }
            
            const dateRetourDeclaree = demande.avis_retour.date_retour_declaree ? 
                new Date(demande.avis_retour.date_retour_declaree).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '';
            const dateRetourEffectif = demande.avis_retour.date_retour_effectif ? 
                new Date(demande.avis_retour.date_retour_effectif).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '';
            
            avisRetourHtml = `
                <div class="mb-2">
                    <small class="text-muted d-block mb-1">
                        <i class="fas fa-plane-arrival me-1" style="color: rgb(238, 181, 135);"></i>
                        <strong>Avis de Retour</strong>
                    </small>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        ${dateRetourDeclaree ? `<small><i class="fas fa-calendar me-1"></i>${dateRetourDeclaree}</small>` : ''}
                        ${dateRetourEffectif ? `<small><i class="fas fa-calendar-check me-1"></i>${dateRetourEffectif}</small>` : ''}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    ${avisRetourBadge}
                </div>
            `;
        }
        
        // Action buttons
        let actionsHtml = '';
        if (demande.is_own && demande.avis_depart && demande.avis_depart.statut === 'pending') {
            actionsHtml = `
                <form action="/leaves/${demande.id}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande?');">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash me-1"></i>Supprimer
                    </button>
                </form>
            `;
        }
        
        // PDF buttons
        let pdfButtonsHtml = '';
        if (demande.avis_depart && demande.avis_depart.pdf_path) {
            pdfButtonsHtml += `
                <a href="/leaves/avis-depart/${demande.avis_depart.id}/download-pdf" 
                   class="btn btn-sm btn-outline-primary" 
                   target="_blank"
                   title="Avis de Départ PDF">
                    <i class="fas fa-file-pdf me-1"></i> PDF Départ
                </a>
            `;
        }
        if (demande.avis_retour && demande.avis_retour.statut_raw == 'approved' && demande.avis_retour.pdf_path) {
            pdfButtonsHtml += `
                <a href="/leaves/avis-retour/${demande.avis_retour.id}/download-pdf" 
                   class="btn btn-sm btn-outline-success" 
                   target="_blank"
                   title="Avis de Retour PDF">
                    <i class="fas fa-file-pdf me-1"></i> PDF Retour
                </a>
            `;
        }
        
        cardsHtml += `
            <div class="col-12">
                <div class="card shadow-sm border-left-4" style="border-left-color: ${borderColor};">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary me-2">#${demande.id}</span>
                                    <h6 class="mb-0 fw-bold text-gray-800">${demande.type}</h6>
                                </div>
                                <div class="text-muted small">
                                    <div><i class="fas fa-calendar-alt me-1"></i> ${dateFormatted}</div>
                                    <div class="mt-1"><i class="fas fa-calendar-day me-1"></i> ${demande.nbr_jours}j</div>
                                </div>
                            </div>
                            <div class="col-md-4 border-start border-end px-3">
                                ${avisDepartHtml}
                            </div>
                            <div class="col-md-3 px-3">
                                ${avisRetourHtml || '<div class="text-muted small">Aucun avis de retour</div>'}
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="d-flex flex-column gap-2 align-items-end">
                                    ${statutBadge}
                                    ${actionsHtml}
                                    ${pdfButtonsHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    if (cardsContainer) cardsContainer.innerHTML = cardsHtml;
    
    // Render table (simplified - you can expand this if needed)
    if (tableContainer) {
        // Table rendering can be added here if needed
        // For now, we'll keep the server-rendered table structure
    }
    
    // Render pagination
    renderPagination(total, startIndex, endIndex, currentPage, totalPages);
}

// Render pagination
function renderPagination(total, startIndex, endIndex, currentPage, totalPages) {
    const paginationContainer = document.getElementById('paginationContainer');
    if (!paginationContainer) return;

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    const paginationHtml = `
        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}" ${currentPage === 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    ${generatePaginationLinks(currentPage, totalPages)}
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    `;
    paginationContainer.innerHTML = paginationHtml;
}

// Generate pagination links
function generatePaginationLinks(currentPage, totalPages) {
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }

    let html = '';
    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
    }

    return html;
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        currentPage = 1;
        filterAndPaginateDemandes();
    }, 300);
}

function updateFilters() {
    currentPage = 1;
    filterAndPaginateDemandes();
}

// Event listeners for filters
document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('year');
    const perPageSelect = document.getElementById('per_page');
    const searchInput = document.getElementById('search');
    
    if (yearSelect) {
        yearSelect.addEventListener('change', updateFilters);
    }
    
    if (perPageSelect) {
        perPageSelect.addEventListener('change', updateFilters);
    }
    
    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.page-link')) {
            e.preventDefault();
            const pageLink = e.target.closest('.page-link');
            const page = parseInt(pageLink.getAttribute('data-page'));
            if (page && page !== currentPage) {
                currentPage = page;
                filterAndPaginateDemandes();
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });
    
    // Initial render
    filterAndPaginateDemandes();
});

// Scroll indicator
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.querySelector('.table-responsive');
    const indicator = document.querySelector('.table-scroll-indicator');
    
    if (tableContainer && indicator) {
        tableContainer.addEventListener('scroll', function() {
            const scrollLeft = this.scrollLeft;
            const maxScrollLeft = this.scrollWidth - this.clientWidth;
            const scrollPercentage = (scrollLeft / maxScrollLeft) * 100;
            
            indicator.style.width = scrollPercentage + '%';
            indicator.style.opacity = scrollLeft > 0 ? '1' : '0';
        });
    }

});
</script>
@endpush
@endsection


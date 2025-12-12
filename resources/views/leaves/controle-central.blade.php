@extends('layouts.app')

@section('title', 'Demandes des Agents de Central')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                Demandes des Agents de Central
            </h1>
            <div>
                <a href="{{ route('hr.leaves.annuel') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hr.leaves.controle-central') }}" class="row align-items-end">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="statut" class="form-label mb-1">Statut</label>
                    <select class="form-select" id="statut" name="statut" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('statut') == 'approved' ? 'selected' : '' }}>Validé</option>
                        <option value="rejected" {{ request('statut') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        <option value="cancelled" {{ request('statut') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label for="year" class="form-label mb-1">Année</label>
                    <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label for="month" class="form-label mb-1">Mois</label>
                    <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                        <option value="">Tous les mois</option>
                        @foreach($months ?? [] as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Demandes Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.9rem; width: 100%;">
                    <thead>
                        <!-- Primary Headers -->
                        <tr>
                            <th colspan="7" class="text-center align-middle" style="background-color: #e9ecef; color: #495057; font-weight: 600; padding: 12px;">
                                Données Demande
                            </th>
                            <th colspan="5" class="text-center align-middle" style="background-color: #28a745; color: white; font-weight: 600; padding: 12px;">
                                Avis de Départ
                            </th>
                            <th colspan="5" class="text-center align-middle" style="background-color: #fd7e14; color: white; font-weight: 600; padding: 12px;">
                                Avis de Retour
                            </th>
                        </tr>
                        <!-- Secondary Headers -->
                        <tr>
                            <!-- Données Demande -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 60px; padding: 10px;">Nº</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px;">Nº demande</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px;">Matricule</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 180px; padding: 10px;">Agent</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px;">Direction</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px;">Entité</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px;">Type Demande</th>
                            <!-- Avis de Départ -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 150px; padding: 10px;">Date Depot</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px;">Nbr Jours Demandés</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px;">Date de Départ</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px;">Date de Retour</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px;">Statut</th>
                            <!-- Avis de Retour -->
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 150px; padding: 10px;">Date Depot</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 120px; padding: 10px;">Nbr Jours Consommés</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px;">Date de Retour Déclarée</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 130px; padding: 10px;">Date Retour Effectif</th>
                            <th class="text-center align-middle" style="background-color: #f8f9fa; min-width: 100px; padding: 10px;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $item)
                            <tr>
                                <!-- Données Demande -->
                                <td class="text-center align-middle" style="padding: 10px;">{{ $loop->iteration }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">Nº {{ is_array($item) ? $item['id'] : $item->id }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">{{ is_array($item) ? $item['matricule'] : ($item->matricule ?? $item->ppr) }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    <a href="{{ route('hr.leaves.user-info', is_array($item) ? $item['ppr'] : ($item->ppr ?? '')) }}" 
                                       class="text-decoration-none" 
                                       style="color: #28a745; font-weight: 500; cursor: pointer;">
                                        {{ is_array($item) ? $item['user_name'] : ($item->user_name ?? 'N/A') }}
                                    </a>
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">{{ is_array($item) ? ($item['direction'] ?? 'N/A') : ($item->direction ?? 'N/A') }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">{{ is_array($item) ? ($item['entite'] ?? 'N/A') : ($item->entite ?? 'N/A') }}</td>
                                <td class="text-center align-middle" style="padding: 10px;">{{ is_array($item) ? ($item['type_demande'] ?? 'C.A.A') : ($item->type_demande ?? 'C.A.A') }}</td>
                                <!-- Avis de Départ -->
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && $item['avis_depart_date_depot'])
                                        {{ \Carbon\Carbon::parse($item['avis_depart_date_depot'])->format('Y-m-d H:i') }}
                                    @elseif(isset($item->avis_depart_date_depot) && $item->avis_depart_date_depot)
                                        {{ \Carbon\Carbon::parse($item->avis_depart_date_depot)->format('Y-m-d H:i') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['nbr_jours_demandes']) && $item['nbr_jours_demandes'])
                                        {{ $item['nbr_jours_demandes'] }}j
                                    @elseif(isset($item->nbr_jours_demandes) && $item->nbr_jours_demandes)
                                        {{ $item->nbr_jours_demandes }}j
                                    @else
                                        <span style="color: #6c757d;">(?)j</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['date_debut']) && $item['date_debut'])
                                        {{ \Carbon\Carbon::parse($item['date_debut'])->format('Y-m-d') }}
                                    @elseif(isset($item->date_debut) && $item->date_debut)
                                        {{ \Carbon\Carbon::parse($item->date_debut)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['date_retour']) && $item['date_retour'])
                                        {{ \Carbon\Carbon::parse($item['date_retour'])->format('Y-m-d') }}
                                    @elseif(isset($item->date_retour) && $item->date_retour)
                                        {{ \Carbon\Carbon::parse($item->date_retour)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['avis_depart_statut_label']) && $item['avis_depart_statut_label'])
                                        <span style="color: #fd7e14; font-weight: 500;">{{ $item['avis_depart_statut_label'] }}</span>
                                    @elseif(isset($item->avis_depart_statut_label) && $item->avis_depart_statut_label)
                                        <span style="color: #fd7e14; font-weight: 500;">{{ $item->avis_depart_statut_label }}</span>
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <!-- Avis de Retour -->
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['avis_retour_date_depot']) && $item['avis_retour_date_depot'])
                                        {{ \Carbon\Carbon::parse($item['avis_retour_date_depot'])->format('Y-m-d H:i') }}
                                    @elseif(isset($item->avis_retour_date_depot) && $item->avis_retour_date_depot)
                                        {{ \Carbon\Carbon::parse($item->avis_retour_date_depot)->format('Y-m-d H:i') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['nbr_jours_consommes']) && $item['nbr_jours_consommes'])
                                        {{ $item['nbr_jours_consommes'] }}j
                                    @elseif(isset($item->nbr_jours_consommes) && $item->nbr_jours_consommes)
                                        {{ $item->nbr_jours_consommes }}j
                                    @else
                                        <span style="color: #6c757d;">(?)j</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['date_retour_declaree']) && $item['date_retour_declaree'])
                                        {{ \Carbon\Carbon::parse($item['date_retour_declaree'])->format('Y-m-d') }}
                                    @elseif(isset($item->date_retour_declaree) && $item->date_retour_declaree)
                                        {{ \Carbon\Carbon::parse($item->date_retour_declaree)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['date_retour_effectif']) && $item['date_retour_effectif'])
                                        {{ \Carbon\Carbon::parse($item['date_retour_effectif'])->format('Y-m-d') }}
                                    @elseif(isset($item->date_retour_effectif) && $item->date_retour_effectif)
                                        {{ \Carbon\Carbon::parse($item->date_retour_effectif)->format('Y-m-d') }}
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="padding: 10px;">
                                    @if(is_array($item) && isset($item['avis_retour_statut_label']) && $item['avis_retour_statut_label'])
                                        <span style="color: #28a745; font-weight: 500;">{{ $item['avis_retour_statut_label'] }}</span>
                                    @elseif(isset($item->avis_retour_statut_label) && $item->avis_retour_statut_label)
                                        <span style="color: #28a745; font-weight: 500;">{{ $item->avis_retour_statut_label }}</span>
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune demande trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($demandes->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-muted">
                                Affichage de {{ $demandes->firstItem() }} à {{ $demandes->lastItem() }} sur {{ $demandes->total() }} demandes
                            </p>
                        </div>
                        <div>
                            {{ $demandes->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

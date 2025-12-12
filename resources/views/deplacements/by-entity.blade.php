@extends('layouts.app')

@section('title', 'Déplacements - ' . $entite->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">{{ $entite->name }}</h1>
            <p class="text-muted mb-0">Période: {{ $periode->name }} | Type: {{ ucfirst($type) }}</p>
        </div>
        <div class="d-flex gap-2">
            @php
                $user = auth()->user();
                // Only chefs (not admins) can prepare periode
                $isChef = $entite->chef_ppr === $user->ppr;
                $isAdmin = $user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
            @endphp
            @if($isChef && !$isAdmin)
                <a href="{{ route('deplacements.preparer-periode', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="btn btn-primary">
                    <i class="fas fa-calendar-plus me-2"></i>Préparer la période
                </a>
            @endif
            <a href="{{ route('deplacements.by-period', ['type' => $type, 'periode' => $periode->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Year Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('deplacements.by-entity', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="row align-items-end">
                <div class="col-md-4">
                    <label for="year" class="form-label mb-1">Année</label>
                    <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Enveloppe consommée</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalSomme, 2, ',', ' ') }} DH</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Bénéficiaires</p>
                            <h4 class="mb-0 fw-bold">{{ $totalBeneficiaires }}/{{ $totalBeneficiairesPossible }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list me-2"></i>Liste des Déplacements
            </h5>
        </div>
        <div class="card-body p-0">
            @if($paginatedData->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">Nº</th>
                                <th class="px-3 py-3">Matricule</th>
                                <th class="px-3 py-3">Nom Complet</th>
                                <th class="px-3 py-3">Nombre de jours</th>
                                <th class="px-3 py-3">Montant</th>
                                <th class="px-3 py-3">Echelle</th>
                                <th class="px-3 py-3">Fonction</th>
                                <th class="px-3 py-3">Entité</th>
                                <th class="px-3 py-3">Ordre de mission</th>
                                <th class="px-3 py-3">Etat de sommes dues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginatedData as $group)
                                @php
                                    $user = $group['user'];
                                    $currentParcours = $user->parcours->where('entite_id', $entite->id)
                                        ->where(function($q) {
                                            $q->whereNull('date_fin')
                                              ->orWhere('date_fin', '>=', now());
                                        })
                                        ->sortByDesc('date_debut')
                                        ->first();
                                    $echelle = $group['echelle'];
                                    $firstDeplacementIn = $group['deplacement_ins'][0] ?? null;
                                @endphp
                                <tr>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-secondary">{{ $paginatedData->firstItem() + $loop->index }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="fw-semibold">{{ $user->ppr }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($user->userInfo && $user->userInfo->photo)
                                                <img src="{{ $user->userInfo->photo_url }}" 
                                                     alt="{{ $user->fname }} {{ $user->lname }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    <span class="text-primary fw-bold small">
                                                        {{ strtoupper(substr($user->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->lname ?? '', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $user->fname }} {{ $user->lname }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-primary">{{ $group['total_nbr_jours'] }} jours</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="fw-semibold text-success" style="white-space: nowrap;">{{ number_format($group['total_somme'], 2, ',', ' ') }} DH</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span>{{ $echelle->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-muted">{{ $currentParcours->poste ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-primary">{{ $entite->name }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-muted">{{ $firstDeplacementIn->objet ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($group['total_somme'] > 0)
                                            <span class="badge bg-success">Payé</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($paginatedData->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 text-muted">
                                    Affichage de {{ $paginatedData->firstItem() }} à {{ $paginatedData->lastItem() }} sur {{ $paginatedData->total() }} résultats
                                </p>
                            </div>
                            <div>
                                {{ $paginatedData->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun déplacement trouvé pour cette entité, période et année</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


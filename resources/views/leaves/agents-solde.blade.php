@extends('layouts.app')

@section('title', 'Solde Actuel de mes Agents')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-wallet me-2"></i>
                Solde Actuel de mes Agents
            </h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Year Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hr.leaves.agents-solde') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="year" class="form-label mb-1">Année</label>
                    <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Agents Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if(count($agentsWithBalance) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">Nº</th>
                                <th class="px-3 py-3">Matricule</th>
                                <th class="px-3 py-3">Nom Complet</th>
                                <th class="px-3 py-3">Entité</th>
                                <th class="px-3 py-3">Reliquat année antérieure</th>
                                <th class="px-3 py-3">Reliquat année courante</th>
                                <th class="px-3 py-3">Jours consommés</th>
                                <th class="px-3 py-3">Solde actuel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agentsWithBalance as $index => $item)
                                @php
                                    $agent = $item['user'];
                                    $balance = $item['balance'];
                                    $currentParcours = $agent->parcours->where(function($q) {
                                            $q->whereNull('date_fin')
                                              ->orWhere('date_fin', '>=', now());
                                        })
                                        ->sortByDesc('date_debut')
                                        ->first();
                                @endphp
                                <tr>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <strong>{{ $agent->ppr }}</strong>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($agent->userInfo && $agent->userInfo->photo)
                                                <img src="{{ $agent->userInfo->photo_url }}" 
                                                     alt="{{ $agent->fname }} {{ $agent->lname }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    <span class="text-dark fw-bold small">
                                                        {{ strtoupper(substr($agent->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr($agent->lname ?? '', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $agent->fname }} {{ $agent->lname }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-primary">{{ $currentParcours && $currentParcours->entite ? $currentParcours->entite->name : 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span>{{ $balance['reliquat_annee_anterieure'] ?? 0 }} jours</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span>{{ $balance['reliquat_annee_courante'] ?? 0 }} jours</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span>{{ $balance['cumul_jours_consommes'] ?? 0 }} jours</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($balance['reste'] > 0)
                                            <span class="badge bg-success fs-6">{{ $balance['reste'] }} jours</span>
                                        @else
                                            <span class="badge bg-danger fs-6">{{ $balance['reste'] }} jours</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun agent trouvé</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


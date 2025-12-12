@extends('layouts.app')

@section('title', 'Espace Contrôle Régional')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-map-marked-alt me-2"></i>
                Espace Contrôle Régional
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
            <form method="GET" action="{{ route('hr.leaves.controle-regional') }}" class="row align-items-end">
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

    <!-- Demandes Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nº</th>
                            <th>Agent</th>
                            <th>Date Dépôt</th>
                            <th>Date Départ</th>
                            <th>Date Retour</th>
                            <th>Nbr Jours</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $item)
                            <tr>
                                <td>{{ is_array($item) ? $item['id'] : $item->id }}</td>
                                <td>
                                    <a href="{{ route('hr.leaves.user-info', is_array($item) ? $item['ppr'] : $item->ppr) }}" 
                                       class="text-decoration-none" 
                                       style="color: inherit; cursor: pointer; font-weight: bold;">
                                        <strong>{{ is_array($item) ? $item['user_name'] : ($item->user ? $item->user->fname . ' ' . $item->user->lname : 'N/A') }}</strong>
                                    </a><br>
                                    <small class="text-muted">PPR: {{ is_array($item) ? $item['ppr'] : $item->ppr }}</small>
                                </td>
                                <td>{{ is_array($item) ? \Carbon\Carbon::parse($item['date_depot'])->format('d/m/Y H:i') : $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ is_array($item) && $item['date_debut'] ? \Carbon\Carbon::parse($item['date_debut'])->format('d/m/Y') : (is_object($item) && $item->date_debut ? $item->date_debut->format('d/m/Y') : 'N/A') }}</td>
                                <td>{{ is_array($item) && $item['date_retour'] ? \Carbon\Carbon::parse($item['date_retour'])->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ is_array($item) ? $item['nbr_jours'] : 0 }} jour(s)</span>
                                </td>
                                <td>
                                    @php
                                        $statut = is_array($item) ? ($item['statut'] ?? 'pending') : ($item->statut ?? 'pending');
                                        $badgeClass = \App\Services\StatusHelperService::getBadgeClass($statut);
                                        $statutLabel = is_array($item) ? ($item['statut_label'] ?? $statut) : ($item->statut_label ?? $statut);
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $statutLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
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




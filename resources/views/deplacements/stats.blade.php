@extends('layouts.app')

@section('title', 'Statistiques des Déplacements')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="fas fa-chart-bar me-2"></i>
                Statistiques des Déplacements
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble des déplacements</p>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-plane text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Déplacements</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalDeplacements) }}</h4>
                            <small class="text-info">
                                <i class="fas fa-calendar me-1"></i>{{ number_format($deplacementsThisYear) }} cette année
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Ce Mois</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($deplacementsThisMonth) }}</h4>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>Déplacements ce mois
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Montant Total</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalAmount, 2, ',', ' ') }} DH</h4>
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>{{ number_format($amountThisYear, 2, ',', ' ') }} DH cette année
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-day text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Jours</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalDays) }}</h4>
                            <small class="text-info">
                                <i class="fas fa-chart-line me-1"></i>{{ number_format($avgDays, 1) }} jours en moyenne
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-building me-2"></i>Par Type d'Entité
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-building text-primary me-2"></i>Central</span>
                        <strong>{{ number_format($centralDeplacements) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-map-marked-alt text-success me-2"></i>Régional</span>
                        <strong>{{ number_format($regionalDeplacements) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-exchange-alt me-2"></i>Par Type
                    </h5>
                    @foreach($deplacementsByType as $type)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $type->type_in_out == 'in' ? 'Interne' : 'Externe' }}</span>
                            <strong>{{ number_format($type->count) }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-money-bill-wave me-2"></i>Montant Ce Mois
                    </h5>
                    <div class="text-center">
                        <h2 class="mb-0 fw-bold text-success">{{ number_format($amountThisMonth, 2, ',', ' ') }} DH</h2>
                        <p class="text-muted mb-0 small">Total ce mois</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Monthly Trends -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-line me-2"></i>Tendances Mensuelles (6 derniers mois)
                    </h5>
                    <canvas id="monthlyDeplacementsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Deplacements by Type -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Déplacements par Type
                    </h5>
                    <canvas id="deplacementsByTypeChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Deplacements by Year -->
    @if($deplacementsByYear->count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-bar me-2"></i>Déplacements par Année
                    </h5>
                    <canvas id="deplacementsByYearChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activities -->
    <div class="row g-3">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-history me-2"></i>Activités Récentes
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Nombre de jours</th>
                                    <th>Montant</th>
                                    <th>Échelle</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDeplacements as $deplacement)
                                <tr>
                                    <td>
                                        <strong>{{ $deplacement->user->fname ?? '' }} {{ $deplacement->user->lname ?? '' }}</strong>
                                        <br>
                                        <small class="text-muted">PPR: {{ $deplacement->ppr }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $deplacement->nbr_jours ?? 0 }} jours</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-success">{{ number_format($deplacement->somme ?? 0, 2, ',', ' ') }} DH</span>
                                    </td>
                                    <td>
                                        {{ $deplacement->echelleTarif->echelle->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $deplacement->type_in_out == 'in' ? 'bg-primary' : 'bg-info' }}">
                                            {{ $deplacement->type_in_out == 'in' ? 'Interne' : 'Externe' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $deplacement->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucune activité récente</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyDeplacementsChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Déplacements',
                data: @json($monthlyDeplacements),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Deplacements by Type Chart
    const typeCtx = document.getElementById('deplacementsByTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: @json($deplacementsByType->map(fn($type) => $type->type_in_out == 'in' ? 'Interne' : 'Externe')),
            datasets: [{
                data: @json($deplacementsByType->pluck('count')),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Deplacements by Year Chart
    @if($deplacementsByYear->count() > 0)
    const yearCtx = document.getElementById('deplacementsByYearChart').getContext('2d');
    new Chart(yearCtx, {
        type: 'bar',
        data: {
            labels: @json($deplacementsByYear->pluck('annee')),
            datasets: [{
                label: 'Nombre de Déplacements',
                data: @json($deplacementsByYear->pluck('count')),
                backgroundColor: 'rgba(59, 130, 246, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection


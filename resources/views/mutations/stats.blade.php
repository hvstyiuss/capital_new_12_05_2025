@extends('layouts.app')

@section('title', 'Statistiques des Mutations')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="fas fa-chart-bar me-2"></i>
                Statistiques des Mutations
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble des demandes de mutation</p>
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
                                <i class="fas fa-exchange-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Mutations</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalMutations) }}</h4>
                            <small class="text-info">
                                <i class="fas fa-calendar me-1"></i>{{ number_format($mutationsThisYear) }} cette année
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
                                <i class="fas fa-clock text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">En Attente</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($pendingMutations) }}</h4>
                            <small class="text-muted">
                                <i class="fas fa-hourglass-half me-1"></i>En attente de traitement
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
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Approuvées</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($approvedMutations) }}</h4>
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>{{ number_format($approvedThisYear) }} cette année
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
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-times-circle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Rejetées</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($rejectedMutations) }}</h4>
                            <small class="text-danger">
                                <i class="fas fa-times me-1"></i>{{ number_format($rejectedThisYear) }} cette année
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
                        <i class="fas fa-calendar-check me-2"></i>Ce Mois
                    </h5>
                    <div class="text-center">
                        <h2 class="mb-0 fw-bold text-primary">{{ number_format($mutationsThisMonth) }}</h2>
                        <p class="text-muted mb-0 small">Mutations ce mois</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-percentage me-2"></i>Taux d'Approximation
                    </h5>
                    <div class="text-center">
                        @php
                            $approvalRate = $totalMutations > 0 ? round(($approvedMutations / $totalMutations) * 100, 1) : 0;
                        @endphp
                        <h2 class="mb-0 fw-bold text-success">{{ $approvalRate }}%</h2>
                        <p class="text-muted mb-0 small">Taux d'approbation</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-line me-2"></i>Cette Année
                    </h5>
                    <div class="text-center">
                        <h2 class="mb-0 fw-bold text-info">{{ number_format($mutationsThisYear) }}</h2>
                        <p class="text-muted mb-0 small">Total cette année</p>
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
                    <canvas id="monthlyMutationsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Mutations by Type -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Mutations par Type
                    </h5>
                    <canvas id="mutationsByTypeChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Chart -->
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-bar me-2"></i>Statut des Mutations
                    </h5>
                    <canvas id="mutationsByStatusChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

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
                                    <th>Type</th>
                                    <th>De</th>
                                    <th>Vers</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMutations as $mutation)
                                <tr>
                                    <td>
                                        <strong>{{ $mutation->user->fname ?? '' }} {{ $mutation->user->lname ?? '' }}</strong>
                                        <br>
                                        <small class="text-muted">PPR: {{ $mutation->ppr }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $mutation->mutation_type == 'interne' ? 'bg-primary' : 'bg-info' }}">
                                            {{ ucfirst($mutation->mutation_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $currentParcours = $mutation->user->parcours->where(function($p) {
                                                return is_null($p->date_fin) || $p->date_fin >= now();
                                            })->sortByDesc('date_debut')->first();
                                        @endphp
                                        <small>{{ $currentParcours->entite->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $mutation->toEntite->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $isApproved = false;
                                            $isRejected = false;
                                            if ($mutation->mutation_type == 'interne') {
                                                $isApproved = $mutation->approved_by_current_direction == 1 && $mutation->rejected_by_current_direction == 0;
                                                $isRejected = $mutation->rejected_by_current_direction == 1;
                                            } else {
                                                $isApproved = $mutation->approved_by_current_direction == 1 && $mutation->approved_by_destination_direction == 1 && $mutation->rejected_by_current_direction == 0 && $mutation->rejected_by_destination_direction == 0;
                                                $isRejected = $mutation->rejected_by_current_direction == 1 || $mutation->rejected_by_destination_direction == 1;
                                            }
                                            $badgeClass = $isApproved ? 'bg-success' : ($isRejected ? 'bg-danger' : 'bg-info');
                                            $statusText = $isApproved ? 'Approuvée' : ($isRejected ? 'Rejetée' : 'En attente');
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $mutation->created_at->format('d/m/Y H:i') }}</small>
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
    const monthlyCtx = document.getElementById('monthlyMutationsChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Mutations',
                data: @json($monthlyMutations),
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

    // Mutations by Type Chart
    const typeCtx = document.getElementById('mutationsByTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: @json($mutationsByType->pluck('mutation_type')->map(fn($type) => ucfirst($type))),
            datasets: [{
                data: @json($mutationsByType->pluck('count')),
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

    // Mutations by Status Chart
    const statusCtx = document.getElementById('mutationsByStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(@json($mutationsByStatus)),
            datasets: [{
                label: 'Nombre de Mutations',
                data: Object.values(@json($mutationsByStatus)),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]
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
</script>
@endpush
@endsection


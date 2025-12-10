@extends('layouts.app')

@section('title', 'Statistiques des Congés')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="fas fa-chart-bar me-2"></i>
                Statistiques des Congés
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble des demandes de congé</p>
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
                                <i class="fas fa-calendar-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Demandes</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalDemandes) }}</h4>
                            <small class="text-info">
                                <i class="fas fa-calendar me-1"></i>{{ number_format($leavesThisYear) }} cette année
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
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">En Attente</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($pendingDemandes) }}</h4>
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
                            <h4 class="mb-0 fw-bold">{{ number_format($approvedDemandes) }}</h4>
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
                            <h4 class="mb-0 fw-bold">{{ number_format($rejectedDemandes) }}</h4>
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
                        <i class="fas fa-building me-2"></i>Par Type d'Entité
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-building text-primary me-2"></i>Central</span>
                        <strong>{{ number_format($centralLeaves) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-map-marked-alt text-success me-2"></i>Régional</span>
                        <strong>{{ number_format($regionalLeaves) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-calendar-check me-2"></i>Ce Mois
                    </h5>
                    <div class="text-center">
                        <h2 class="mb-0 fw-bold text-primary">{{ number_format($leavesThisMonth) }}</h2>
                        <p class="text-muted mb-0 small">Demandes ce mois</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-ban me-2"></i>Annulées
                    </h5>
                    <div class="text-center">
                        <h2 class="mb-0 fw-bold text-secondary">{{ number_format($cancelledDemandes) }}</h2>
                        <p class="text-muted mb-0 small">Total annulées</p>
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
                    <canvas id="monthlyLeavesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Leaves by Type -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Congés par Type
                    </h5>
                    <canvas id="leavesByTypeChart" height="100"></canvas>
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
                        <i class="fas fa-chart-bar me-2"></i>Statut des Congés
                    </h5>
                    <canvas id="leavesByStatusChart" height="60"></canvas>
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
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeaves as $leave)
                                <tr>
                                    <td>
                                        <strong>{{ $leave->user->fname }} {{ $leave->user->lname }}</strong>
                                        <br>
                                        <small class="text-muted">PPR: {{ $leave->ppr }}</small>
                                    </td>
                                    <td>
                                        {{ $leave->demandeConge->typeConge->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($leave->statut) {
                                                'approved' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                'cancelled' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($leave->statut) }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $leave->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucune activité récente</td>
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
    const monthlyCtx = document.getElementById('monthlyLeavesChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Demandes de Congé',
                data: @json($monthlyLeaves),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
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

    // Leaves by Type Chart
    const typeCtx = document.getElementById('leavesByTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: @json($leavesByType->pluck('name')),
            datasets: [{
                data: @json($leavesByType->pluck('count')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
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

    // Leaves by Status Chart
    const statusCtx = document.getElementById('leavesByStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(@json($leavesByStatus)),
            datasets: [{
                label: 'Nombre de Demandes',
                data: Object.values(@json($leavesByStatus)),
                backgroundColor: [
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
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


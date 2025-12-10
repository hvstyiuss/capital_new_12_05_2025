@extends('layouts.app')

@section('title', 'Tableau de Bord - Capital')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Tableau de Bord</h1>
            <p class="text-muted mb-0">Bienvenue sur Capital HR - Votre plateforme de gestion des ressources humaines</p>
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
                                <i class="fas fa-users text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Utilisateurs</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['total_users'] ?? 0) }}</h4>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>{{ number_format($stats['active_users'] ?? 0) }} actifs
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
                                <i class="fas fa-calendar-alt text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Total Demandes</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['total_demandes'] ?? 0) }}</h4>
                            <small class="text-secondary">
                                <i class="fas fa-clock me-1"></i>{{ number_format($stats['pending_demandes'] ?? 0) }} en attente
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
                                <i class="fas fa-exchange-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Mutations</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['total_mutations'] ?? 0) }}</h4>
                            <small class="text-secondary">
                                <i class="fas fa-hourglass-half me-1"></i>{{ number_format($stats['pending_mutations'] ?? 0) }} en attente
                            </small>
                            <br>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>{{ number_format($stats['approved_mutations'] ?? 0) }} approuvées
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
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-building text-secondary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Entités</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['total_entites'] ?? 0) }}</h4>
                            <small class="text-secondary">
                                <i class="fas fa-user-tie me-1"></i>{{ number_format($stats['entites_with_chefs'] ?? 0) }} avec chefs
                            </small>
                            <br>
                            <small class="text-danger">
                                <i class="fas fa-user-slash me-1"></i>{{ number_format($stats['entites_without_chefs'] ?? 0) }} postes vacants
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Nouveaux Utilisateurs</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['new_users_30d'] ?? 0) }}</h5>
                            <small class="text-muted">30 derniers jours</small>
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
                                <i class="fas fa-check-circle text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Congés Approuvés</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['approved_demandes'] ?? 0) }}</h5>
                            <small class="text-muted">Total approuvés</small>
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
                            <p class="text-muted mb-0 small">Congés Rejetés</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['rejected_demandes'] ?? 0) }}</h5>
                            <small class="text-muted">Total rejetés</small>
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
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-tag text-secondary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0 small">Affectations</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_affectations'] ?? 0) }}</h5>
                            <small class="text-muted">{{ number_format($stats['affectations_this_month'] ?? 0) }} ce mois</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mutations and Deplacements Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>Statistiques des Mutations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Total Mutations</p>
                                <h3 class="mb-0 fw-bold text-primary">{{ number_format($stats['total_mutations'] ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">En Attente</p>
                                <h3 class="mb-0 fw-bold text-secondary">{{ number_format($stats['pending_mutations'] ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Approuvées</p>
                                <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['approved_mutations'] ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Taux d'Approximation</p>
                                <h3 class="mb-0 fw-bold text-secondary">
                                    @php
                                        $totalMutations = $stats['total_mutations'] ?? 0;
                                        $approvedMutations = $stats['approved_mutations'] ?? 0;
                                        $approvalRate = $totalMutations > 0 ? round(($approvedMutations / $totalMutations) * 100, 1) : 0;
                                    @endphp
                                    {{ $approvalRate }}%
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-plane me-2 text-primary"></i>Statistiques des Déplacements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Total Déplacements</p>
                                <h3 class="mb-0 fw-bold text-primary">{{ number_format($stats['total_deplacements'] ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Cette Année</p>
                                <h3 class="mb-0 fw-bold text-secondary">{{ number_format($stats['deplacements_this_year'] ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Ce Mois</p>
                                <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['deplacements_this_month'] ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-1 small">Montant Total ({{ now()->year }})</p>
                                <h3 class="mb-0 fw-bold text-primary">
                                    {{ number_format($stats['deplacement_amount_this_year'] ?? 0, 2) }} <small class="fs-6">MAD</small>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Monthly Trends Chart -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Tendances Mensuelles (6 derniers mois)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Leave Status Distribution -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-pie me-2 text-success"></i>Statut des Congés
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="leaveStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Leaves by Type -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-bar me-2 text-secondary"></i>Congés par Type
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="leavesByTypeChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Users by Grade -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-users-cog me-2 text-primary"></i>Utilisateurs par Grade (Top 10)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="usersByGradeChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Entities and Recent Activities -->
    <div class="row g-3 mb-4">
        <!-- Top Entities -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-building me-2 text-primary"></i>Entités par Effectif
                        </h5>
                        @if(isset($stats['top_entites']) && $stats['top_entites']->total() > 0)
                        <small class="text-muted">
                            {{ $stats['top_entites']->firstItem() }}-{{ $stats['top_entites']->lastItem() }} sur {{ $stats['top_entites']->total() }}
                        </small>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Entité</th>
                                    <th class="text-end">Effectif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['top_entites'] ?? [] as $index => $entite)
                                    @php
                                        $entityNumber = isset($stats['top_entites']) && $stats['top_entites']->firstItem() 
                                            ? $stats['top_entites']->firstItem() + $index 
                                            : $index + 1;
                                    @endphp
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $entityNumber }}</span></td>
                                        <td>{{ $entite->name }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ $entite->count }} agents</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucune donnée disponible</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($stats['top_entites']) && $stats['top_entites']->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $stats['top_entites']->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2 text-secondary"></i>Activités Récentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($stats['recent_activities'] ?? [] as $activity)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        @if($activity['type'] === 'leave')
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-calendar-check text-success"></i>
                                            </div>
                                        @else
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-exchange-alt text-primary"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-semibold small">{{ $activity['user'] }}</div>
                                        <div class="text-muted small">
                                            @if($activity['type'] === 'leave')
                                                Congé: {{ $activity['details'] }}
                                            @else
                                                Mutation vers: {{ $activity['details'] }}
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $activity['date']->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">Aucune activité récente</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fas fa-bolt text-primary me-2"></i>Actions Rapides
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('hr.users.create') }}'">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                        <i class="fas fa-user-plus text-primary fs-4"></i>
                    </div>
                    <h6 class="fw-semibold mb-1 small">Nouvel Utilisateur</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('hr.users.index') }}'">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                        <i class="fas fa-users text-success fs-4"></i>
                    </div>
                    <h6 class="fw-semibold mb-1 small">Gérer les Utilisateurs</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('hr.leaves.index') }}'">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                        <i class="fas fa-calendar-check text-success fs-4"></i>
                    </div>
                    <h6 class="fw-semibold mb-1 small">Demandes de Congé</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('mutations.tracking') }}'">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                        <i class="fas fa-exchange-alt text-primary fs-4"></i>
                    </div>
                    <h6 class="fw-semibold mb-1 small">Mutations</h6>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyTrendsChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($stats['months'] ?? []),
                datasets: [
                    {
                        label: 'Nouveaux Utilisateurs',
                        data: @json($stats['monthly_users'] ?? []),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Demandes de Congé',
                        data: @json($stats['monthly_leaves'] ?? []),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Mutations',
                        data: @json($stats['monthly_mutations'] ?? []),
                        borderColor: 'rgb(234, 179, 8)',
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Leave Status Chart
    const leaveStatusCtx = document.getElementById('leaveStatusChart');
    if (leaveStatusCtx) {
        new Chart(leaveStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Approuvés', 'En Attente', 'Rejetés'],
                datasets: [{
                    data: [
                        {{ $stats['approved_demandes'] ?? 0 }},
                        {{ $stats['pending_demandes'] ?? 0 }},
                        {{ $stats['rejected_demandes'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }

    // Leaves by Type Chart
    const leavesByTypeCtx = document.getElementById('leavesByTypeChart');
    if (leavesByTypeCtx) {
        new Chart(leavesByTypeCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($stats['leaves_by_type'] ?? [])->pluck('name')->toArray()),
                datasets: [{
                    label: 'Nombre de Congés',
                    data: @json(collect($stats['leaves_by_type'] ?? [])->pluck('count')->toArray()),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
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
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Users by Grade Chart
    const usersByGradeCtx = document.getElementById('usersByGradeChart');
    if (usersByGradeCtx) {
        new Chart(usersByGradeCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($stats['users_by_grade'] ?? [])->pluck('name')->toArray()),
                datasets: [{
                    label: 'Nombre d\'Utilisateurs',
                    data: @json(collect($stats['users_by_grade'] ?? [])->pluck('count')->toArray()),
                    backgroundColor: 'rgba(234, 179, 8, 0.8)',
                    borderColor: 'rgb(234, 179, 8)',
                    borderWidth: 1
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush
@endsection

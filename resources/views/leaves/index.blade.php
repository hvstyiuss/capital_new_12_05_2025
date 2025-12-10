@extends('layouts.app')

@section('title', 'Demandes de Congés')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Demandes de Congés</h1>
                <p class="text-muted mb-0">Gérez vos demandes de congés par catégorie</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    <i class="fas fa-calendar me-2"></i>
                    <span class="fw-bold">{{ date('Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Categories -->
    <div class="row g-4">
        <!-- 1. Congé Administratif Annuel -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="text-success fw-bold fs-5">1</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Congé Administratif Annuel</h5>
                                <small class="text-muted">Congé Administratif</small>
                            </div>
                        </div>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Disponible
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-lg-3">
                            <a href="{{ route('hr.leaves.annuel') }}" class="text-decoration-none">
                                <div class="card border border-success border-2 h-100 hover-lift">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-calendar-check text-success fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Congé Administratif</h6>
                                        <small class="text-muted">Annuel</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Congé Maladie -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="text-warning fw-bold fs-5">2</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Congé Maladie</h5>
                                <small class="text-muted">Différents types de congés maladie</small>
                            </div>
                        </div>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Disponible
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Congé Maternité -->
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('hr.leaves.create-maternite') }}" class="text-decoration-none">
                                <div class="card border border-pink border-2 h-100 hover-lift">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-pink bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-baby text-pink fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Congé Maternité</h6>
                                        <small class="text-muted">14 semaines (98 jours)</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Congé Maladie Courte Durée -->
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('hr.leaves.create-maladie') }}" class="text-decoration-none">
                                <div class="card border border-info border-2 h-100 hover-lift">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-heartbeat text-info fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Congé Maladie Courte Durée</h6>
                                        <small class="text-muted">(Congé ≤ 6mois)</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Congé Maladie Moyenne Durée -->
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('hr.leaves.create-maladie') }}" class="text-decoration-none">
                                <div class="card border border-warning border-2 h-100 hover-lift">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-stethoscope text-warning fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Congé Maladie Moyenne Durée</h6>
                                        <small class="text-muted">(Congé ≤ 3ans)</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Congé Maladie Longue Durée -->
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('hr.leaves.create-maladie') }}" class="text-decoration-none">
                                <div class="card border border-danger border-2 h-100 hover-lift">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-hospital text-danger fs-4"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Congé Maladie Longue Durée</h6>
                                        <small class="text-muted">(Congé ≤ 5ans)</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Congé Exceptionnel -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="text-primary fw-bold fs-5">3</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Congé Exceptionnel</h5>
                                <small class="text-muted">Congés pour événements spéciaux</small>
                            </div>
                        </div>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Disponible
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Mariage -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-purple bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-heart text-purple fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Mariage</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Naissance -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-baby-carriage text-info fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Naissance</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Décès -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-quran text-secondary fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Décès</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Circoncision -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-star text-success fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Circoncision</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Hospitalisation -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-hospital-alt text-danger fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Hospitalisation</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Pélerinage -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-mosque text-warning fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Pélerinage</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Mandat représentatif -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border h-100 opacity-75">
                                <div class="card-body text-center p-4">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-handshake text-primary fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Mandat représentatif</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .bg-pink {
        background-color: #fce7f3;
    }

    .text-pink {
        color: #ec4899;
    }

    .bg-purple {
        background-color: #f3e8ff;
    }

    .text-purple {
        color: #a855f7;
    }

    .card {
        transition: all 0.2s ease;
    }

    .card-header {
        padding: 1rem 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }
</style>
@endpush
@endsection

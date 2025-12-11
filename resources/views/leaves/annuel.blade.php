@extends('layouts.app')

@section('title', 'Congé Administratif Annuel')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Congé Administratif Annuel</h1>
                <p class="text-muted mb-0">Gestion de vos congés administratifs annuels</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6 px-3 py-2">{{ date('Y') }}/{{ date('Y', strtotime('+1 year')) }}</span>
            </div>
        </div>
    </div>

    <!-- Remaining Days Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <h6 class="text-muted text-uppercase small mb-3 fw-semibold">JOURS RESTANTS</h6>
            <h1 class="display-4 fw-bold text-primary mb-2">{{ $leaveData['jours_restants'] ?? 0 }}</h1>
            <p class="text-muted mb-0 fs-5">jours à consommer</p>
        </div>
    </div>

    <!-- Leave Details Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted small mb-2 fw-semibold">Référence Décision</h6>
                    <p class="mb-0 fw-bold fs-5">{{ $leaveData['reference_decision'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted small mb-2 fw-semibold">Reliquat Année Antérieure</h6>
                    <p class="mb-0 fw-bold text-success fs-5">{{ $leaveData['reliquat_annee_anterieure'] ?? 0 }}j</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted small mb-2 fw-semibold">Reliquat Année Courante</h6>
                    <p class="mb-0 fw-bold text-secondary fs-5">{{ $leaveData['reliquat_annee_courante'] ?? 0 }}j</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted small mb-2 fw-semibold">Jours Consommés</h6>
                    <p class="mb-0 fw-bold text-danger fs-5">{{ $leaveData['cumul_jours_consommes'] ?? 0 }}j</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($hasReturnDateToday)
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Date de retour prévue : Aujourd'hui</strong>
            <p class="mb-0 mt-1">Vous devez déclarer votre date de retour dès maintenant.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($hasPendingAvisDepart && !$hasReturnDateToday)
        <div class="alert alert-info alert-dismissible show mb-4" id="pendingAvisDepartAlert" role="alert" data-no-auto-hide="true" data-alert-key="pending-avis-depart-alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <i class="fas fa-info-circle me-2"></i>
            <strong>En attente de validation</strong>
            <p class="mb-0 mt-1">Vous ne pouvez pas déclarer votre retour tant que votre chef n'a pas validé l'avis de départ.</p>
            @if($pendingDemande)
                <form action="{{ route('hr.leaves.destroy', $pendingDemande->id) }}" method="POST" class="d-inline mt-2" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash-alt me-1"></i>Supprimer la demande
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Actions Disponibles -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">Actions Disponibles</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <div>
                    @if($hasRemainingLeave && !$hasPendingDemande && !$hasApprovedDemandeWithoutRetour && !$hasPendingAvisRetour)
                        <a href="{{ route('hr.leaves.create') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Avis de Départ
                        </a>
                    @else
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-arrow-left me-2"></i>Avis de Départ
                            @if($hasPendingDemande)
                                <small class="d-block mt-1 fw-normal">Demande en attente</small>
                            @elseif($hasApprovedDemandeWithoutRetour)
                                <small class="d-block mt-1 fw-normal">Déclarez d'abord votre date de retour</small>
                            @elseif($hasPendingAvisRetour)
                                <small class="d-block mt-1 fw-normal">En attente de validation</small>
                            @else
                                <small class="d-block mt-1 fw-normal">Non disponible</small>
                            @endif
                        </button>
                    @endif
                </div>
                
                <div>
                    @if($hasReturnDateToday)
                        <a href="{{ route('hr.leaves.declare-retour') }}" class="btn btn-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>Déclarer Date de Retour
                        </a>
                    @elseif($hasDemandeWithoutRetour)
                        <a href="{{ route('hr.leaves.declare-retour') }}" class="btn btn-success">
                            <i class="fas fa-arrow-right me-2"></i>Avis de Retour
                        </a>
                    @elseif($hasPendingAvisDepart)
                        <button class="btn btn-warning" disabled>
                            <i class="fas fa-arrow-right me-2"></i>Avis de Retour
                            <small class="d-block mt-1 fw-normal">En attente de validation</small>
                        </button>
                    @else
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-arrow-right me-2"></i>Avis de Retour
                            <small class="d-block mt-1 fw-normal">Non disponible</small>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Persistent dismissal for pendingAvisDepartAlert
    const pendingAvisDepartAlert = document.getElementById('pendingAvisDepartAlert');
    if (pendingAvisDepartAlert) {
        const alertKey = pendingAvisDepartAlert.getAttribute('data-alert-key');
        if (localStorage.getItem(alertKey) === 'dismissed') {
            pendingAvisDepartAlert.remove();
        } else {
            pendingAvisDepartAlert.addEventListener('closed.bs.alert', function () {
                localStorage.setItem(alertKey, 'dismissed');
            });
        }
    }
});
</script>
@endpush
@endsection

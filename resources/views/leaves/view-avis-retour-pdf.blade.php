@extends('layouts.app')

@section('title', 'Avis de Retour - PDF avec Solde')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">
                    <i class="fas fa-file-pdf text-success me-2"></i>
                    Avis de Retour - PDF avec Solde
                </h1>
                <p class="text-muted mb-0">Visualisation de l'avis de retour avec les informations de solde</p>
            </div>
            <div>
                <a href="{{ $isOwner ? route('leaves.tracking') : route('hr.leaves.agents') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
                @if($pdfUrl)
                <a href="{{ route('hr.leaves.download-avis-retour-pdf', $avisRetour->id) }}" class="btn btn-success" target="_blank">
                    <i class="fas fa-download me-2"></i>Télécharger PDF
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Solde Information Cards -->
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
                    <p class="mb-0 fw-bold text-info fs-5">{{ $leaveData['reliquat_annee_courante'] ?? 0 }}j</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted small mb-2 fw-semibold">Jours Restants</h6>
                    <p class="mb-0 fw-bold text-primary fs-5">{{ $leaveData['jours_restants'] ?? 0 }}j</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Avis de Retour Information -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-info-circle text-success me-2"></i>
                Informations de l'Avis de Retour
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Collaborateur</label>
                        <p class="fw-bold mb-0">{{ $user->fname }} {{ $user->lname }}</p>
                        <small class="text-muted">PPR: {{ $user->ppr }}</small>
                    </div>
                </div>
                @if($avisDepart)
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Date de Départ</label>
                        <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($avisDepart->date_depart)->format('d/m/Y') }}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Date de Retour Déclarée</label>
                        <p class="fw-bold mb-0">{{ $avisRetour->date_retour_declaree ? \Carbon\Carbon::parse($avisRetour->date_retour_declaree)->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Date de Retour Effectif</label>
                        <p class="fw-bold mb-0">{{ $avisRetour->date_retour_effectif ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Nombre de Jours Consommés</label>
                        <p class="fw-bold mb-0">{{ $avisRetour->nbr_jours_consumes ?? 0 }} jour(s)</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Date de Dépôt</label>
                        <p class="fw-bold mb-0">{{ $avisRetour->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Statut</label>
                        <span class="badge bg-success">Validé</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-file-pdf text-success me-2"></i>
                Aperçu de l'avis de retour
            </h5>
        </div>
        <div class="card-body p-0">
            @if($pdfUrl)
                <iframe src="{{ $pdfUrl }}" 
                        style="width: 100%; height: 800px; border: none;" 
                        title="Avis de Retour PDF">
                </iframe>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-pdf fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Le PDF n'est pas encore disponible.</p>
                    <p class="text-muted small">Le PDF sera généré automatiquement après validation de l'avis de retour.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection



@props(['viewModel'])

<div class="d-flex flex-column gap-2 align-items-end">
    <a href="{{ route('hr.leaves.show', $viewModel->demande->id) }}" 
       class="btn btn-sm btn-outline-primary" 
       title="Voir les détails"
       aria-label="Voir les détails de la demande">
        <i class="fas fa-eye me-1" aria-hidden="true"></i>
        Détails
    </a>
    
    @if($viewModel->isPending())
        <a href="{{ route('hr.leaves.show', $viewModel->demande->id) }}" 
           class="btn btn-sm btn-primary"
           aria-label="Traiter la demande">
            <i class="fas fa-cog me-1" aria-hidden="true"></i>
            Traiter
        </a>
    @else
        @if($viewModel->isFullyApproved())
            @if($viewModel->hasConsumptionExceeds() && $viewModel->avisRetour?->id)
                <a href="{{ route('hr.leaves.download-explanation-pdf', $viewModel->avisRetour->id) }}" 
                   class="btn btn-sm btn-outline-danger" 
                   target="_blank"
                   title="Note d'explication"
                   aria-label="Télécharger la note d'explication PDF">
                    <i class="fas fa-file-pdf me-1" aria-hidden="true"></i>
                    PDF
                </a>
            @else
                <span class="badge bg-success">OK</span>
            @endif
        @endif
    @endif
    
    @if($viewModel->hasConsumptionExceeds())
        <i class="fas fa-question-circle text-danger" 
           title="Consommation supérieure à la date de retour déclarée"
           aria-label="Avertissement: consommation supérieure à la date de retour déclarée"></i>
    @endif
</div>


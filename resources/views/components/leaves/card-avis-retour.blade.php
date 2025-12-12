@props(['viewModel'])

<div class="mb-2">
    <small class="text-muted d-block mb-1">
        <i class="fas fa-plane-arrival me-1 text-avis-retour" aria-hidden="true"></i>
        <strong>Avis de Retour</strong>
    </small>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @if($viewModel->avisRetour?->nbr_jours_consumes)
            <span class="badge bg-info">{{ $viewModel->avisRetour->nbr_jours_consumes }}j</span>
        @endif
        @if($viewModel->avisRetour?->formatted_date_retour_declaree)
            <small>
                <i class="fas fa-calendar me-1" aria-hidden="true"></i>
                {{ $viewModel->avisRetour->formatted_date_retour_declaree }}
            </small>
        @endif
        @if($viewModel->avisRetour?->formatted_date_retour_effectif)
            <small>
                <i class="fas fa-calendar-check me-1" aria-hidden="true"></i>
                {{ $viewModel->avisRetour->formatted_date_retour_effectif }}
            </small>
        @endif
    </div>
</div>

<div class="d-flex flex-column gap-2">
    <div class="d-flex align-items-center gap-2">
        @if($viewModel->avisRetour?->statut_label)
            <span class="badge {{ $viewModel->avisRetour->badge_class }}">{{ $viewModel->avisRetour->statut_label }}</span>
        @endif
        
        @if($viewModel->avisRetour?->can_download_pdf && $viewModel->avisRetour->pdf_route_name)
            <a href="{{ route($viewModel->avisRetour->pdf_route_name, $viewModel->avisRetour->id) }}" 
               class="{{ $viewModel->avisRetour->pdf_path ? 'text-success' : 'text-danger' }}" 
               target="_blank"
               title="{{ $viewModel->avisRetour->pdf_path ? 'Avis de Retour PDF' : 'Note d\'Explication PDF' }}"
               aria-label="{{ $viewModel->avisRetour->pdf_path ? 'Télécharger l\'avis de retour PDF' : 'Télécharger la note d\'explication PDF' }}">
                <i class="fas fa-file-pdf" aria-hidden="true"></i>
            </a>
        @endif
    </div>
    
    @if($viewModel->avisRetour?->can_be_validated)
        <form action="{{ route('hr.leaves.validate-avis-retour', $viewModel->avisRetour->id) }}" 
              method="POST" 
              class="d-inline" 
              onsubmit="return confirm('Êtes-vous sûr de vouloir valider cet avis de retour?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" aria-label="Valider l'avis de retour">
                <i class="fas fa-check me-1" aria-hidden="true"></i>
                Valider
            </button>
        </form>
    @endif
</div>


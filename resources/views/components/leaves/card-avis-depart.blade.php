@props(['viewModel'])

<div class="mb-2">
    <small class="text-muted d-block mb-1">
        <i class="fas fa-plane-departure me-1 text-avis-depart" aria-hidden="true"></i>
        <strong>Avis de Départ</strong>
    </small>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @if($viewModel->avisDepart?->nb_jours_demandes)
            <span class="badge bg-primary">{{ $viewModel->avisDepart->nb_jours_demandes }}j</span>
        @endif
        @if($viewModel->avisDepart?->formatted_date_depart)
            <small>
                <i class="fas fa-calendar me-1" aria-hidden="true"></i>
                {{ $viewModel->avisDepart->formatted_date_depart }}
            </small>
        @endif
        @if($viewModel->avisDepart?->formatted_date_retour)
            <small>
                <i class="fas fa-calendar-check me-1" aria-hidden="true"></i>
                {{ $viewModel->avisDepart->formatted_date_retour }}
            </small>
        @endif
    </div>
</div>

<div class="d-flex align-items-center gap-2">
    @if($viewModel->avisDepart?->statut_label)
        <span class="badge {{ $viewModel->avisDepart->badge_class }}">{{ $viewModel->avisDepart->statut_label }}</span>
    @endif
    
    @if($viewModel->avisDepart?->can_download_pdf)
        <a href="{{ route('hr.leaves.download-avis-depart-pdf', $viewModel->avisDepart->id) }}" 
           class="text-danger" 
           target="_blank"
           title="Avis de Départ PDF"
           aria-label="Télécharger l'avis de départ PDF">
            <i class="fas fa-file-pdf" aria-hidden="true"></i>
        </a>
    @endif
    
    @if($viewModel->avisDepart?->can_be_validated)
        <form action="{{ route('hr.leaves.validate-avis-depart', $viewModel->avisDepart->id) }}" 
              method="POST" 
              class="d-inline" 
              onsubmit="return confirm('Êtes-vous sûr de vouloir valider cet avis de départ?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" aria-label="Valider l'avis de départ">
                <i class="fas fa-check me-1" aria-hidden="true"></i>
                Valider
            </button>
        </form>
    @endif
</div>


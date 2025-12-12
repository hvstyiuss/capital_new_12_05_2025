<div class="col-12">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3" aria-hidden="true"></i>
            <p class="text-muted mb-0">Aucune demande trouvée</p>
            
            @if(request('statut') || request('year'))
                <a href="{{ route('hr.leaves.agents') }}" 
                   class="btn btn-sm btn-outline-primary mt-3"
                   aria-label="Réinitialiser les filtres de recherche">
                    <i class="fas fa-redo me-2" aria-hidden="true"></i>
                    Réinitialiser les filtres
                </a>
            @endif
        </div>
    </div>
</div>


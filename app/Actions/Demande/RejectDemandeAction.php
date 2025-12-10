<?php

namespace App\Actions\Demande;

use App\Models\Demande;
use App\Services\DemandeService;

class RejectDemandeAction
{
    protected DemandeService $demandeService;

    public function __construct(DemandeService $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    public function execute(Demande $demande): Demande
    {
        // Reject the avis de départ if it exists
        if ($demande->avis && $demande->avis->avisDepart) {
            $demande->avis->avisDepart->update(['statut' => 'rejected']);
        }
        
        return $demande->fresh();
    }
}





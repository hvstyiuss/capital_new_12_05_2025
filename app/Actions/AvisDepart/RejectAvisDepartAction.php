<?php

namespace App\Actions\AvisDepart;

use App\Models\AvisDepart;
use App\Models\User;
use DomainException;

class RejectAvisDepartAction
{
    public function execute(AvisDepart $avisDepart, User $rejector, callable $isChefOfUserCallback): AvisDepart
    {
        $demande = $avisDepart->avis->demande;
        $demandeUser = $demande->user;
        $isChef = $isChefOfUserCallback($rejector, $demandeUser);
        
        if (!$isChef && !$rejector->hasRole('admin')) {
            throw new DomainException('Vous n\'avez pas l\'autorisation de rejeter cet avis de départ.');
        }

        $avisDepart->update(['statut' => 'rejected']);

        return $avisDepart->fresh();
    }
}





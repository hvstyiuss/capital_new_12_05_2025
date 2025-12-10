<?php

namespace App\Actions\Conge;

use App\Models\User;
use App\Models\Demande;
use App\Services\CongeService;
use Carbon\Carbon;

class CalculateCongeBalanceAction
{
    protected CongeService $congeService;

    public function __construct(CongeService $congeService)
    {
        $this->congeService = $congeService;
    }

    public function execute(User $user): array
    {
        $currentYear = Carbon::now()->year;
        
        // Calculate balance
        $balance = $this->congeService->calculateAnnualBalance($user->ppr, $currentYear);
        
        // Check if user has any pending demandes
        $pendingDemande = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'pending');
            })
            ->with(['avis.avisDepart'])
            ->first();
        
        $hasPendingDemande = $pendingDemande !== null;
        
        // Check if user has any demandes with validated avisDepart but without avis_retour
        $hasDemandeWithoutRetour = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'approved');
            })
            ->whereHas('avis', function($query) {
                $query->whereDoesntHave('avisRetour');
            })
            ->exists();
        
        // Check if user has any pending avis de départ
        $hasPendingAvisDepart = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'pending');
            })
            ->whereHas('avis', function($query) {
                $query->whereDoesntHave('avisRetour');
            })
            ->exists();
        
        // Check if user has any approved demandes without retour
        $hasApprovedDemandeWithoutRetour = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'approved');
            })
            ->whereHas('avis', function($query) {
                $query->whereDoesntHave('avisRetour');
            })
            ->exists();
        
        // Check if user has any pending avis de retour
        $hasPendingAvisRetour = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisRetour', function($query) {
                $query->where('statut', 'pending');
            })
            ->exists();
        
        // Check if user has demandes with return date today
        $demandesWithReturnToday = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'approved')
                      ->whereDate('date_retour', Carbon::today());
            })
            ->whereHas('avis', function($query) {
                $query->whereDoesntHave('avisRetour');
            })
            ->with(['avis.avisDepart'])
            ->get();
        
        return array_merge($balance, [
            'has_pending_demande' => $hasPendingDemande,
            'has_demande_without_retour' => $hasDemandeWithoutRetour,
            'has_pending_avis_depart' => $hasPendingAvisDepart,
            'has_approved_demande_without_retour' => $hasApprovedDemandeWithoutRetour,
            'has_pending_avis_retour' => $hasPendingAvisRetour,
            'demandes_with_return_today' => $demandesWithReturnToday,
        ]);
    }
}





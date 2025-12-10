<?php

namespace App\Actions\Demande;

use App\Models\Demande;
use App\Models\User;
use App\Services\DemandeService;
use App\Services\CongeService;
use App\Services\NotificationService;
use Carbon\Carbon;
use DomainException;

class RejectAsChefAction
{
    protected DemandeService $demandeService;
    protected CongeService $congeService;
    protected NotificationService $notificationService;

    public function __construct(
        DemandeService $demandeService,
        CongeService $congeService,
        NotificationService $notificationService
    ) {
        $this->demandeService = $demandeService;
        $this->congeService = $congeService;
        $this->notificationService = $notificationService;
    }

    public function execute(Demande $demande, User $chef, string $rejectionReason, callable $isChefOfUserCallback): Demande
    {
        $demandeUser = $demande->user;
        $isChef = $isChefOfUserCallback($chef, $demandeUser);
        
        if (!$isChef && !$chef->hasRole('admin')) {
            throw new DomainException('Vous n\'avez pas l\'autorisation de rejeter cette demande.');
        }

        // Get the avis and avisDepart to calculate days to refund
        $demande->load('avis.avisDepart');
        $avis = $demande->avis;
        $avisDepart = $avis ? $avis->avisDepart : null;
        $nbJours = 0;
        
        if ($avisDepart) {
            $nbJours = $avisDepart->nb_jours_demandes ?? 0;
        }

        // Reject the avis de départ if it exists
        $wasPending = false;
        if ($avis && $avisDepart) {
            $wasPending = $avisDepart->statut === 'pending';
            $avisDepart->update(['statut' => 'rejected']);
        }
        
        // Refund days to solde if avis de départ was pending (days were already consumed)
        if ($wasPending && $nbJours > 0) {
            $this->congeService->refundDays($demandeUser->ppr, $nbJours);
        }
        
        // Notify the user
        $message = "Votre demande de congé du " . Carbon::parse($demande->date_debut)->format('d/m/Y') . " a été rejetée.";
        if ($rejectionReason) {
            $message .= " Raison: " . $rejectionReason;
        }
        if ($nbJours > 0) {
            $message .= " Les {$nbJours} jour(s) ont été remboursés à votre solde.";
        }
        
        $this->notificationService->sendToUser(
            $demandeUser,
            'leave_rejected',
            'Demande de congé rejetée',
            $message,
            ['demande_id' => $demande->id],
            [
                'action_url' => route('leaves.tracking'),
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
            ]
        );

        return $demande->fresh();
    }
}





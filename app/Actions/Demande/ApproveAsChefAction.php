<?php

namespace App\Actions\Demande;

use App\Models\Demande;
use App\Models\User;
use App\Services\DemandeService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DomainException;

class ApproveAsChefAction
{
    protected DemandeService $demandeService;
    protected NotificationService $notificationService;

    public function __construct(DemandeService $demandeService, NotificationService $notificationService)
    {
        $this->demandeService = $demandeService;
        $this->notificationService = $notificationService;
    }

    public function execute(Demande $demande, User $chef, callable $isChefOfUserCallback, callable $generatePdfCallback = null): Demande
    {
        $demandeUser = $demande->user;
        $isChef = $isChefOfUserCallback($chef, $demandeUser);
        
        if (!$isChef && !$chef->hasRole('admin')) {
            throw new DomainException('Vous n\'avez pas l\'autorisation d\'approuver cette demande.');
        }

        // Approve the avis de départ if it exists
        if ($demande->avis && $demande->avis->avisDepart) {
            $avisDepart = $demande->avis->avisDepart;
            $avisDepart->update(['statut' => 'approved']);
            
            // Generate PDF if not already generated
            if ($demandeUser && $generatePdfCallback) {
                if (Schema::hasColumn('avis_departs', 'pdf_path')) {
                    if (!$avisDepart->pdf_path) {
                        $pdfPath = $generatePdfCallback($avisDepart, $demandeUser);
                        $avisDepart->update(['pdf_path' => $pdfPath]);
                    }
                } else {
                    // Column doesn't exist, generate PDF but don't save path
                    $generatePdfCallback($avisDepart, $demandeUser);
                }
            }
        }
        
        // Notify the user
        $this->notificationService->sendToUser(
            $demandeUser,
            'leave_approved',
            'Demande de congé approuvée',
            "Votre demande de congé du " . Carbon::parse($demande->date_debut)->format('d/m/Y') . " a été approuvée.",
            ['demande_id' => $demande->id],
            [
                'action_url' => route('leaves.tracking'),
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
            ]
        );

        return $demande->fresh();
    }
}





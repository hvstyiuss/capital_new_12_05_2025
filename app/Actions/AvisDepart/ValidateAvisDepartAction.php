<?php

namespace App\Actions\AvisDepart;

use App\Models\AvisDepart;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Schema;
use DomainException;

class ValidateAvisDepartAction
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function execute(AvisDepart $avisDepart, User $validator, callable $isChefOfUserCallback, callable $generatePdfCallback = null): AvisDepart
    {
        $demande = $avisDepart->avis->demande;
        $demandeUser = $demande->user;
        $isChef = $isChefOfUserCallback($validator, $demandeUser);
        
        if (!$isChef && !$validator->hasRole('admin')) {
            throw new DomainException('Vous n\'avez pas l\'autorisation de valider cet avis de départ.');
        }

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

        return $avisDepart->fresh();
    }
}





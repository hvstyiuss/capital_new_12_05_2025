<?php

namespace App\Actions\AvisRetour;

use App\Models\AvisRetour;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DomainException;

class ValidateAvisRetourAction
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function execute(
        AvisRetour $avisRetour,
        User $validator,
        ?string $dateRetourEffectif,
        callable $isChefOfUserCallback,
        callable $generatePdfCallback = null,
        callable $generateExplanationPdfCallback = null
    ): AvisRetour {
        $demande = $avisRetour->avis->demande;
        $demandeUser = $demande->user;
        $isChef = $isChefOfUserCallback($validator, $demandeUser);
        
        if (!$isChef && !$validator->hasRole('admin')) {
            throw new DomainException('Vous n\'avez pas l\'autorisation de valider cet avis de retour.');
        }

        // Update date_retour_effectif if provided
        $updateData = ['statut' => 'approved'];
        if ($dateRetourEffectif) {
            $updateData['date_retour_effectif'] = $dateRetourEffectif;
        }
        $avisRetour->update($updateData);

        // Reload the model to get the latest data
        $avisRetour->refresh();

        // Get avis and avisDepart
        $avis = $avisRetour->avis;
        $avisDepart = $avis ? $avis->avisDepart : null;
        
        // Generate PDF for avis de retour if not already generated
        if ($demandeUser && $generatePdfCallback) {
            if (Schema::hasColumn('avis_retours', 'pdf_path')) {
                if (!$avisRetour->pdf_path) {
                    $pdfPath = $generatePdfCallback($avisRetour, $demandeUser, $avisDepart);
                    $avisRetour->update(['pdf_path' => $pdfPath]);
                }
            } else {
                // Column doesn't exist, generate PDF but don't save path
                $generatePdfCallback($avisRetour, $demandeUser, $avisDepart);
            }
        }

        // Generate explanation PDF if return date is different from declared return date
        if ($avisRetour->date_retour_declaree && $avisRetour->date_retour_effectif) {
            $dateRetourDeclaree = Carbon::parse($avisRetour->date_retour_declaree);
            $dateRetourEffectif = Carbon::parse($avisRetour->date_retour_effectif);
            
            if (!$dateRetourDeclaree->equalTo($dateRetourEffectif) && $generateExplanationPdfCallback) {
                if (Schema::hasColumn('avis_retours', 'explanation_pdf_path')) {
                    if (!$avisRetour->explanation_pdf_path) {
                        $explanationPdfPath = $generateExplanationPdfCallback($avisRetour, $demandeUser, $avisDepart);
                        $avisRetour->update(['explanation_pdf_path' => $explanationPdfPath]);
                    }
                } else {
                    // Column doesn't exist, generate PDF but don't save path
                    $generateExplanationPdfCallback($avisRetour, $demandeUser, $avisDepart);
                }
            }
        }

        return $avisRetour->fresh();
    }
}





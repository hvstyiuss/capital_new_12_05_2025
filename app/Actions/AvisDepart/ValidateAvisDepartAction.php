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
        
        // Generate PDF (on-the-fly, not stored)
        if ($demandeUser && $generatePdfCallback) {
            try {
                // Generate PDF but don't save path (PDFs are generated on-the-fly)
                $generatePdfCallback($avisDepart, $demandeUser);
            } catch (\Exception $e) {
                // Log error but don't fail validation
                \Log::error('Error generating avis depart PDF during validation: ' . $e->getMessage(), [
                    'avis_depart_id' => $avisDepart->id,
                    'exception' => get_class($e),
                ]);
            }
        }

        return $avisDepart->fresh();
    }
}





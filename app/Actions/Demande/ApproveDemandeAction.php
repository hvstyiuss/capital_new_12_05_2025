<?php

namespace App\Actions\Demande;

use App\Models\Demande;
use App\Services\DemandeService;
use Illuminate\Support\Facades\Schema;

class ApproveDemandeAction
{
    protected DemandeService $demandeService;

    public function __construct(DemandeService $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    public function execute(Demande $demande, callable $generatePdfCallback = null): Demande
    {
        // Approve the avis de départ if it exists
        if ($demande->avis && $demande->avis->avisDepart) {
            $avisDepart = $demande->avis->avisDepart;
            $avisDepart->update(['statut' => 'approved']);
            
            // Generate PDF if not already generated
            if ($demande->user && $generatePdfCallback) {
                if (Schema::hasColumn('avis_departs', 'pdf_path')) {
                    if (!$avisDepart->pdf_path) {
                        $pdfPath = $generatePdfCallback($avisDepart, $demande->user);
                        $avisDepart->update(['pdf_path' => $pdfPath]);
                    }
                } else {
                    // Column doesn't exist, generate PDF but don't save path
                    $generatePdfCallback($avisDepart, $demande->user);
                }
            }
        }
        
        return $demande->fresh();
    }
}





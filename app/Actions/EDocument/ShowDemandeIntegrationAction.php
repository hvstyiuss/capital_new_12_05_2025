<?php

namespace App\Actions\EDocument;

use Illuminate\Support\Facades\File;

class ShowDemandeIntegrationAction
{
    /**
     * Prepare data for displaying the integration request model.
     */
    public function execute(): array
    {
        $pdfPath   = public_path('edocuments/demande_integration.pdf');
        $pdfExists = File::exists($pdfPath);

        return [
            'pdfExists' => $pdfExists,
            'pdfUrl'    => $pdfExists ? asset('edocuments/demande_integration.pdf') : null,
        ];
    }
}






<?php

namespace App\Actions\EDocument;

use Illuminate\Support\Facades\File;

class ShowStatutPersonnelAction
{
    /**
     * Prepare data for displaying Statut du Personnel.
     */
    public function execute(): array
    {
        $pdfPath   = public_path('edocuments/statut_du_personnel.pdf');
        $pdfExists = File::exists($pdfPath);

        return [
            'pdfExists' => $pdfExists,
            'pdfUrl'    => $pdfExists ? asset('edocuments/statut_du_personnel.pdf') : null,
        ];
    }
}






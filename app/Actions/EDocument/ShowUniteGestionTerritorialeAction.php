<?php

namespace App\Actions\EDocument;

use Illuminate\Support\Facades\File;

class ShowUniteGestionTerritorialeAction
{
    /**
     * Prepare data for displaying Unité de Gestion Territoriale.
     */
    public function execute(): array
    {
        $pdfPath   = public_path('edocuments/unites_civil.pdf');
        $pdfExists = File::exists($pdfPath);

        return [
            'pdfExists' => $pdfExists,
            'pdfUrl'    => $pdfExists ? asset('edocuments/unites_civil.pdf') : null,
        ];
    }
}






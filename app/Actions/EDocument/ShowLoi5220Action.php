<?php

namespace App\Actions\EDocument;

use Illuminate\Support\Facades\File;

class ShowLoi5220Action
{
    /**
     * Prepare data for displaying Loi N°52.20.
     */
    public function execute(): array
    {
        $pdfPath1 = public_path('edocuments/loi-5220.pdf');
        $pdfPath2 = public_path('edocuments/loi_52_20.pdf');

        if (File::exists($pdfPath1)) {
            return [
                'pdfExists' => true,
                'pdfUrl'    => asset('edocuments/loi-5220.pdf'),
            ];
        }

        if (File::exists($pdfPath2)) {
            return [
                'pdfExists' => true,
                'pdfUrl'    => asset('edocuments/loi_52_20.pdf'),
            ];
        }

        return [
            'pdfExists' => false,
            'pdfUrl'    => null,
        ];
    }
}






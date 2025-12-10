<?php

namespace App\Actions\Annonce;

use App\Models\Annonce;
use App\Services\AnnonceService;

class DeleteAnnonceAction
{
    protected AnnonceService $annonceService;

    public function __construct(AnnonceService $annonceService)
    {
        $this->annonceService = $annonceService;
    }

    public function execute(Annonce $annonce): void
    {
        $this->annonceService->delete($annonce);
    }
}





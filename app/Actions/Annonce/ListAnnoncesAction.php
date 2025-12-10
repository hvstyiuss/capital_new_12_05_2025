<?php

namespace App\Actions\Annonce;

use App\Services\AnnonceService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAnnoncesAction
{
    protected AnnonceService $annonceService;

    public function __construct(AnnonceService $annonceService)
    {
        $this->annonceService = $annonceService;
    }

    public function execute(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->annonceService->getAll($filters, $perPage);
    }
}





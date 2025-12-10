<?php

namespace App\Actions\Parcours;

use App\Models\Parcours;
use App\Services\ParcoursService;

class DeleteParcoursAction
{
    protected ParcoursService $parcoursService;

    public function __construct(ParcoursService $parcoursService)
    {
        $this->parcoursService = $parcoursService;
    }

    public function execute(Parcours $parcours): void
    {
        $this->parcoursService->delete($parcours);
    }
}






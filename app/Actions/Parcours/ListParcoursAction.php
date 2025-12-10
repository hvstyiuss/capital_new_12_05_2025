<?php

namespace App\Actions\Parcours;

use App\Services\ParcoursService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListParcoursAction
{
    protected ParcoursService $parcoursService;

    public function __construct(ParcoursService $parcoursService)
    {
        $this->parcoursService = $parcoursService;
    }

    public function execute(array $filters): LengthAwarePaginator
    {
        return $this->parcoursService->getAll($filters);
    }
}






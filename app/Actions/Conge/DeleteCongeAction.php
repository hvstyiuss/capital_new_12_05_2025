<?php

namespace App\Actions\Conge;

use App\Models\Conge;
use App\Services\CongeService;

class DeleteCongeAction
{
    protected CongeService $congeService;

    public function __construct(CongeService $congeService)
    {
        $this->congeService = $congeService;
    }

    /**
     * Delete a conge.
     */
    public function execute(Conge $conge): void
    {
        $this->congeService->delete($conge);
    }
}






<?php

namespace App\Actions\Conge;

use App\Models\Conge;
use App\DTOs\Conge\CreateCongeDTO;
use App\Services\CongeService;

class CreateCongeAction
{
    protected CongeService $congeService;

    public function __construct(CongeService $congeService)
    {
        $this->congeService = $congeService;
    }

    /**
     * Create a new conge from DTO.
     */
    public function execute(CreateCongeDTO $dto): Conge
    {
        return $this->congeService->create([
            'ppr' => $dto->ppr,
            'annee' => $dto->annee,
        ]);
    }
}



<?php

namespace App\Actions\Conge;

use App\Models\Conge;
use App\DTOs\Conge\UpdateCongeDTO;
use App\Services\CongeService;

class UpdateCongeAction
{
    protected CongeService $congeService;

    public function __construct(CongeService $congeService)
    {
        $this->congeService = $congeService;
    }

    /**
     * Update an existing conge from DTO.
     */
    public function execute(Conge $conge, UpdateCongeDTO $dto): Conge
    {
        $data = array_filter([
            'annee' => $dto->annee,
        ], fn($value) => $value !== null);

        return $this->congeService->update($conge, $data);
    }
}



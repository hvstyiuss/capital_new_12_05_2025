<?php

namespace App\Actions\Parcours;

use App\Models\Parcours;
use App\DTOs\Parcours\UpdateParcoursDTO;
use App\Services\ParcoursService;

class UpdateParcoursAction
{
    protected ParcoursService $parcoursService;

    public function __construct(ParcoursService $parcoursService)
    {
        $this->parcoursService = $parcoursService;
    }

    public function execute(Parcours $parcours, UpdateParcoursDTO $dto): Parcours
    {
        $data = array_filter([
            'ppr' => $dto->ppr,
            'entite_id' => $dto->entiteId,
            'poste' => $dto->poste,
            'role' => $dto->role,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'grade_id' => $dto->gradeId,
            'reason' => $dto->reason,
            'created_by_ppr' => $dto->createdByPpr,
        ], fn($value) => $value !== null);

        return $this->parcoursService->update($parcours, $data);
    }
}




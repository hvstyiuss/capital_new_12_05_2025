<?php

namespace App\Actions\Parcours;

use App\Models\Parcours;
use App\DTOs\Parcours\CreateParcoursDTO;
use App\Services\ParcoursService;

class CreateParcoursAction
{
    protected ParcoursService $parcoursService;

    public function __construct(ParcoursService $parcoursService)
    {
        $this->parcoursService = $parcoursService;
    }

    public function execute(CreateParcoursDTO $dto): Parcours
    {
        return $this->parcoursService->create([
            'ppr' => $dto->ppr,
            'entite_id' => $dto->entiteId,
            'poste' => $dto->poste,
            'role' => $dto->role,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'grade_id' => $dto->gradeId,
            'reason' => $dto->reason,
            'created_by_ppr' => $dto->createdByPpr,
        ]);
    }
}




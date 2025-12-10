<?php

namespace App\Actions\Annonce;

use App\Models\Annonce;
use App\Models\User;
use App\DTOs\Annonce\UpdateAnnonceDTO;
use App\Services\AnnonceService;

class UpdateAnnonceAction
{
    protected AnnonceService $annonceService;

    public function __construct(AnnonceService $annonceService)
    {
        $this->annonceService = $annonceService;
    }

    public function execute(Annonce $annonce, UpdateAnnonceDTO $dto, User $user): Annonce
    {
        $data = array_filter([
            'ppr' => $dto->ppr ?? $user->ppr,
            'content' => $dto->content,
            'type_annonce_id' => $dto->typeAnnonceId,
            'statut' => $dto->statut,
            'entites' => $dto->entites,
        ], fn($value) => $value !== null);

        return $this->annonceService->update($annonce, $data, $user, $dto->image);
    }
}



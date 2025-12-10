<?php

namespace App\Actions\Annonce;

use App\Models\Annonce;
use App\Models\User;
use App\DTOs\Annonce\CreateAnnonceDTO;
use App\Services\AnnonceService;

class CreateAnnonceAction
{
    protected AnnonceService $annonceService;

    public function __construct(AnnonceService $annonceService)
    {
        $this->annonceService = $annonceService;
    }

    public function execute(CreateAnnonceDTO $dto, User $user): Annonce
    {
        return $this->annonceService->create([
            'ppr' => $dto->ppr ?? $user->ppr,
            'content' => $dto->content,
            'type_annonce_id' => $dto->typeAnnonceId,
            'statut' => $dto->statut ?? 'active',
            'entites' => $dto->entites,
        ], $user, $dto->image);
    }
}



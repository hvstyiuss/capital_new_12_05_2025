<?php

namespace App\DTOs\Conge;

readonly class CreateMaladieDTO
{
    public function __construct(
        public string $ppr,
        public int $typeMaladieId,
        public string $dateDeclaration,
        public ?string $dateConstatation = null,
        public string $dateDepart,
        public ?string $dateRetour = null,
        public int $nbrJoursDemandes,
        public ?string $referenceArret = null,
        public ?string $observation = null
    ) {}
}




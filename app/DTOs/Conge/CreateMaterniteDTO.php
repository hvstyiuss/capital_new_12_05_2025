<?php

namespace App\DTOs\Conge;

readonly class CreateMaterniteDTO
{
    public function __construct(
        public string $ppr,
        public string $dateDeclaration,
        public string $dateDepart,
        public ?string $dateRetour = null,
        public int $nbrJoursDemandes = 98,
        public ?string $observation = null
    ) {}
}




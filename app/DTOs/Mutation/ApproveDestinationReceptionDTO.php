<?php

namespace App\DTOs\Mutation;

readonly class ApproveDestinationReceptionDTO
{
    public function __construct(
        public string $dateDebutAffectation,
    ) {}
}





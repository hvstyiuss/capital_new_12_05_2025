<?php

namespace App\DTOs\Mutation;

readonly class CreateMutationDTO
{
    public function __construct(
        public string $ppr,
        public int $toEntiteId,
        public string $mutationType,
        public string $motif,
        public ?string $motifAutre,
    ) {}
}





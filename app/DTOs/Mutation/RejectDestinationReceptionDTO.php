<?php

namespace App\DTOs\Mutation;

readonly class RejectDestinationReceptionDTO
{
    public function __construct(
        public string $rejectionReasonSuperRh,
    ) {}
}





<?php

namespace App\DTOs\Mutation;

readonly class RejectMutationDTO
{
    public function __construct(
        public string $rejectionType, // 'current' or 'destination'
        public ?string $rejectionReason,
    ) {}
}





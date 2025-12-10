<?php

namespace App\DTOs\Mutation;

readonly class ApproveMutationDTO
{
    public function __construct(
        public string $approvalType, // 'current' or 'destination'
    ) {}
}





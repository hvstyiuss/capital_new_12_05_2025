<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\Models\User;
use App\Services\MutationService;
use DomainException;

class RejectMutationAction
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    public function execute(Mutation $mutation, User $rejector, \App\DTOs\Mutation\RejectMutationDTO $dto): Mutation
    {
        // Check authorization
        $authorization = $this->mutationService->canRejectMutation($mutation, $rejector, $dto->rejectionType);
        if (!$authorization['can_approve']) {
            throw new DomainException($authorization['error']);
        }

        // Reject the mutation
        return $this->mutationService->rejectMutation($mutation, $rejector, $dto->rejectionType, $dto->rejectionReason ?? '')
            ->load(['user', 'toEntite']);
    }
}





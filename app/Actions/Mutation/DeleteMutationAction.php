<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\Services\MutationService;

class DeleteMutationAction
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    /**
     * Delete a mutation.
     */
    public function execute(Mutation $mutation): void
    {
        $this->mutationService->delete($mutation);
    }
}






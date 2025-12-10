<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;

class ShowMutationAction
{
    /**
     * Load relations for a single mutation.
     */
    public function execute(Mutation $mutation): Mutation
    {
        return $mutation->load([
            'user',
            'toEntite',
            'approvedByCurrentDirection',
            'approvedByDestinationDirection',
            'approvedBySuperCollaborateurRh',
        ]);
    }
}






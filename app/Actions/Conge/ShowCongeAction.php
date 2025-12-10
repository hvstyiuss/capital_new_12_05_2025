<?php

namespace App\Actions\Conge;

use App\Models\Conge;

class ShowCongeAction
{
    /**
     * Load relations for a single conge.
     */
    public function execute(Conge $conge): Conge
    {
        return $conge->load(['user']);
    }
}






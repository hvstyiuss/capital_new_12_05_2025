<?php

namespace App\Actions\Parcours;

use App\Models\Parcours;

class ShowParcoursAction
{
    /**
     * Load relations for a single parcours.
     */
    public function execute(Parcours $parcours): Parcours
    {
        return $parcours->load(['user.userInfo', 'entite', 'grade']);
    }
}






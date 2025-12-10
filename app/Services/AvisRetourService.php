<?php

namespace App\Services;

use App\Models\AvisRetour;

class AvisRetourService
{
    /**
     * Create a new avis retour.
     */
    public function create(array $data): AvisRetour
    {
        return AvisRetour::create($data);
    }

    /**
     * Update an avis retour.
     */
    public function update(AvisRetour $avisRetour, array $data): AvisRetour
    {
        $avisRetour->update($data);
        return $avisRetour->fresh();
    }

    /**
     * Delete an avis retour.
     */
    public function delete(AvisRetour $avisRetour): bool
    {
        return $avisRetour->delete();
    }
}





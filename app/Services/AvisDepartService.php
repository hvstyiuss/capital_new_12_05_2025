<?php

namespace App\Services;

use App\Models\AvisDepart;

class AvisDepartService
{
    /**
     * Create a new avis depart.
     */
    public function create(array $data): AvisDepart
    {
        return AvisDepart::create($data);
    }

    /**
     * Update an avis depart.
     */
    public function update(AvisDepart $avisDepart, array $data): AvisDepart
    {
        $avisDepart->update($data);
        return $avisDepart->fresh();
    }

    /**
     * Delete an avis depart.
     */
    public function delete(AvisDepart $avisDepart): bool
    {
        return $avisDepart->delete();
    }
}














<?php

namespace App\Services;

use App\Models\Avis;

class AvisService
{
    /**
     * Create a new avis.
     */
    public function create(array $data): Avis
    {
        return Avis::create($data);
    }

    /**
     * Update an avis.
     */
    public function update(Avis $avis, array $data): Avis
    {
        $avis->update($data);
        return $avis->fresh();
    }

    /**
     * Delete an avis.
     */
    public function delete(Avis $avis): bool
    {
        return $avis->delete();
    }
}






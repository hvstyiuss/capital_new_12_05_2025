<?php

namespace App\Services;

use App\Models\Demande;

class DemandeService
{
    /**
     * Create a new demande.
     */
    public function create(array $data): Demande
    {
        return Demande::create($data);
    }

    /**
     * Update a demande.
     */
    public function update(Demande $demande, array $data): Demande
    {
        $demande->update($data);
        return $demande->fresh();
    }

    /**
     * Delete a demande.
     */
    public function delete(Demande $demande): bool
    {
        return $demande->delete();
    }
}














<?php

namespace App\Actions\Conge;

use App\Models\User;
use App\Models\Parcours;
use App\Models\TypeMaladie;

class PrepareCreateMaladieFormAction
{
    /**
     * Prepare data for the create maladie form.
     */
    public function execute(User $user): array
    {
        // Get user's current entity
        $parcours = Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with('entite')
            ->first();
        
        $entite = $parcours ? $parcours->entite : null;
        
        // Get type maladie options
        $typeMaladies = TypeMaladie::all();
        
        return [
            'typeMaladies' => $typeMaladies,
            'entite' => $entite,
        ];
    }
}




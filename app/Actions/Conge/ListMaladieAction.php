<?php

namespace App\Actions\Conge;

use App\Models\User;
use App\Models\Demande;
use App\Models\DemandeConge;
use App\Models\CongeMaladie;
use Illuminate\Pagination\LengthAwarePaginator;

class ListMaladieAction
{
    /**
     * Get all maladie leave requests for a user.
     */
    public function execute(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Demande::where('ppr', $user->ppr)
            ->where('type', 'conge')
            ->whereHas('demandeConge.typeConge', function($q) {
                $q->where('name', 'maladie');
            })
            ->with([
                'demandeConge.congeMaladie.typeMaladie',
                'demandeConge.typeConge',
                'avis.avisDepart',
                'avis.avisRetour'
            ])
            ->orderBy('created_at', 'desc');

        // Filter by year if provided
        if (isset($filters['year']) && $filters['year']) {
            $query->whereYear('created_at', $filters['year']);
        }

        // Filter by status if provided
        if (isset($filters['status']) && $filters['status']) {
            $query->where('statut', $filters['status']);
        }

        // Search
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('demandeConge.congeMaladie.typeMaladie', function($typeQ) use ($search) {
                    $typeQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('demandeConge.congeMaladie', function($maladieQ) use ($search) {
                    $maladieQ->where('reference_arret', 'like', "%{$search}%")
                             ->orWhere('observation', 'like', "%{$search}%");
                });
            });
        }

        return $query->paginate($perPage);
    }
}




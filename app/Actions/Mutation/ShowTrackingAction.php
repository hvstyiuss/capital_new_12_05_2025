<?php

namespace App\Actions\Mutation;

use App\Models\User;
use App\Services\MutationService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowTrackingAction
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    public function execute(User $user, array $filters = []): array
    {
        $year = $filters['year'] ?? date('Y');
        $perPage = $filters['per_page'] ?? 25;
        $search = $filters['search'] ?? '';

        $relations = ['toEntite', 'approvedByCurrentDirection', 'approvedByDestinationDirection', 'approvedBySuperCollaborateurRh'];

        // Get all mutations for frontend filtering (last 10 years)
        $allMutations = $this->mutationService->getAllMutationsForTracking($user->ppr, $relations);

        // Transform all mutations for frontend
        $allMutationsData = $allMutations->map(function($mutation) {
            return $this->mutationService->transformMutationForFrontend($mutation);
        });

        // Apply filters for initial display (backward compatibility)
        $filteredMutations = $allMutations
            ->when($year, function($collection) use ($year) {
                return $collection->filter(function($mutation) use ($year) {
                    return $mutation->created_at->year == $year;
                });
            })
            ->when($search, function($collection) use ($search) {
                return $collection->filter(function($mutation) use ($search) {
                    return $mutation->toEntite && 
                           stripos($mutation->toEntite->name, $search) !== false;
                });
            })
            ->sortByDesc('created_at')
            ->values();
        
        // Create a simple paginated collection for backward compatibility
        $currentPage = 1;
        $offset = ($currentPage - 1) * $perPage;
        $mutations = new LengthAwarePaginator(
            $filteredMutations->slice($offset, $perPage),
            $filteredMutations->count(),
            $perPage,
            $currentPage
        );

        // Transform mutations for view
        $items = $mutations->getCollection()->map(function($mutation) {
            $statut = $this->mutationService->calculateMutationStatus($mutation);
            
            return [
                'id' => $mutation->id,
                'to_entite_name' => $mutation->toEntite ? $mutation->toEntite->name : 'N/A',
                'date_depot' => $mutation->created_at,
                'mutation_type' => $mutation->mutation_type,
                'statut' => $statut,
                'approved_by_current_direction' => $mutation->approved_by_current_direction,
                'approved_by_destination_direction' => $mutation->approved_by_destination_direction,
                'approved_by_super_collaborateur_rh' => $mutation->approved_by_super_collaborateur_rh,
                'rejected_by_current_direction' => $mutation->rejected_by_current_direction,
                'rejected_by_destination_direction' => $mutation->rejected_by_destination_direction,
                'rejected_by_super_rh' => $mutation->rejected_by_super_rh,
                'date_debut_affectation' => $mutation->date_debut_affectation,
                'approved_by_current_direction_ppr' => $mutation->approved_by_current_direction_ppr,
                'approved_by_destination_direction_ppr' => $mutation->approved_by_destination_direction_ppr,
                'approved_by_super_collaborateur_rh_ppr' => $mutation->approved_by_super_collaborateur_rh_ppr,
                'approved_by_current_direction_at' => $mutation->approved_by_current_direction_at,
                'approved_by_destination_direction_at' => $mutation->approved_by_destination_direction_at,
                'approved_by_super_collaborateur_rh_at' => $mutation->approved_by_super_collaborateur_rh_at,
                'rejection_reason_current' => $mutation->rejection_reason_current,
                'rejection_reason_destination' => $mutation->rejection_reason_destination,
                'rejection_reason_super_rh' => $mutation->rejection_reason_super_rh,
                'is_validated_ent' => $mutation->is_validated_ent ?? false,
                'valide_reception' => $mutation->valide_reception ?? false,
                'valide_par' => $mutation->valide_par,
                'decision_conducteur_rh' => $mutation->decision_conducteur_rh,
                'valide_par_current' => $mutation->approvedByCurrentDirection ? $mutation->approvedByCurrentDirection->nom . ' ' . $mutation->approvedByCurrentDirection->prenom : null,
                'valide_par_destination' => $mutation->approvedByDestinationDirection ? $mutation->approvedByDestinationDirection->nom . ' ' . $mutation->approvedByDestinationDirection->prenom : null,
            ];
        });

        // Check if user has pending mutations
        $hasPendingMutation = $this->mutationService->hasPendingMutations($user->ppr);

        // Get statistics
        $statistics = $this->mutationService->getTrackingStatistics($user->ppr);

        return [
            'items' => $items,
            'hasPendingMutation' => $hasPendingMutation,
            'totalMutations' => $statistics['total'],
            'pendingMutations' => $statistics['pending'],
            'approvedMutations' => $statistics['approved'],
            'rejectedMutations' => $statistics['rejected'],
            'year' => $year,
            'perPage' => $perPage,
            'search' => $search,
            'allMutationsData' => $allMutationsData,
        ];
    }
}


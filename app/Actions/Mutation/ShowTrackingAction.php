<?php

namespace App\Actions\Mutation;

use App\Models\User;
use App\Services\MutationService;
use App\Services\StatusHelperService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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
            
            // Prepare CSS classes, icon, and validation flags
            $statutLower = strtolower($statut);
            $badgeClass = match(true) {
                in_array($statutLower, ['rejeté', 'rejected']) => 'bg-danger',
                in_array($statutLower, ['validé', 'approved']) => 'bg-success',
                $statutLower == 'en attente validation rh' => 'bg-warning text-dark',
                in_array($statutLower, ['en attente', 'pending']) => 'bg-warning text-dark',
                $statutLower == 'en cours de traitement' => 'bg-info',
                default => 'bg-secondary',
            };
            
            $iconClass = match(true) {
                in_array($statutLower, ['rejeté', 'rejected']) => 'fa-times-circle',
                in_array($statutLower, ['validé', 'approved']) => 'fa-check-circle',
                $statutLower == 'en attente validation rh' => 'fa-user-tie',
                in_array($statutLower, ['en attente', 'pending']) => 'fa-clock',
                $statutLower == 'en cours de traitement' => 'fa-spinner',
                default => 'fa-info-circle',
            };
            
            // Check if current entity/direction has approved
            $isCurrentEntityValidated = false;
            if (isset($mutation->approved_by_current_direction)) {
                $isCurrentEntityValidated = $mutation->approved_by_current_direction;
            } elseif (isset($mutation->is_validated_ent)) {
                $isCurrentEntityValidated = $mutation->is_validated_ent;
            }
            
            // For external mutations: check if destination direction has approved
            // For internal mutations: N/A (doesn't apply)
            // For old mutations: use valide_reception field
            $isDestinationEntityValidated = false;
            if ($mutation->mutation_type === 'externe') {
                $isDestinationEntityValidated = $mutation->approved_by_destination_direction ?? false;
            } elseif ($mutation->mutation_type === 'interne') {
                $isDestinationEntityValidated = null; // Internal mutations don't have destination entity validation
            } else {
                // Old mutations: use valide_reception field
                $isDestinationEntityValidated = $mutation->valide_reception ?? false;
            }
            
            // Check if mutation can be deleted
            // Can delete if status is "En attente" (pending) and not approved or rejected
            $canDelete = false;
            if (in_array($statutLower, ['en attente', 'pending'])) {
                if (!$mutation->approved_by_current_direction && 
                    !$mutation->approved_by_destination_direction &&
                    !$mutation->rejected_by_current_direction &&
                    !$mutation->rejected_by_destination_direction) {
                    $canDelete = true;
                }
            }
            
            // Format dates
            $dateDepotFormatted = $mutation->created_at ? Carbon::parse($mutation->created_at)->format('d/m/Y') : null;
            $dateDepotTimeFormatted = $mutation->created_at ? Carbon::parse($mutation->created_at)->format('H:i') : null;
            $dateDebutAffectationFormatted = $mutation->date_debut_affectation ? Carbon::parse($mutation->date_debut_affectation)->format('d/m/Y') : null;
            
            return [
                'id' => $mutation->id,
                'to_entite_name' => $mutation->toEntite ? $mutation->toEntite->name : 'N/A',
                'date_depot' => $mutation->created_at,
                'date_depot_formatted' => $dateDepotFormatted,
                'date_depot_time_formatted' => $dateDepotTimeFormatted,
                'mutation_type' => $mutation->mutation_type,
                'statut' => $statut,
                'statut_lower' => $statutLower,
                'badge_class' => $badgeClass,
                'icon_class' => $iconClass,
                'is_current_entity_validated' => $isCurrentEntityValidated,
                'is_destination_entity_validated' => $isDestinationEntityValidated,
                'can_delete' => $canDelete,
                'approved_by_current_direction' => $mutation->approved_by_current_direction,
                'approved_by_destination_direction' => $mutation->approved_by_destination_direction,
                'approved_by_super_collaborateur_rh' => $mutation->approved_by_super_collaborateur_rh,
                'rejected_by_current_direction' => $mutation->rejected_by_current_direction,
                'rejected_by_destination_direction' => $mutation->rejected_by_destination_direction,
                'rejected_by_super_rh' => $mutation->rejected_by_super_rh,
                'date_debut_affectation' => $mutation->date_debut_affectation,
                'date_debut_affectation_formatted' => $dateDebutAffectationFormatted,
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

        // Generate available years (current year and 5 previous years)
        $currentYear = (int) date('Y');
        $availableYears = [];
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $availableYears[] = $y;
        }

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
            'availableYears' => $availableYears,
        ];
    }
}


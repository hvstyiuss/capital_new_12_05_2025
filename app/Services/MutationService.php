<?php

namespace App\Services;

use App\Models\Mutation;
use App\Models\Entite;
use App\Models\User;
use App\Models\Parcours;
use App\Repositories\MutationRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MutationService
{
    protected MutationRepository $mutationRepository;

    public function __construct(MutationRepository $mutationRepository)
    {
        $this->mutationRepository = $mutationRepository;
    }

    /**
     * Load parent chain recursively for an entity.
     */
    public function loadParentChain($entite, $maxDepth = 10)
    {
        if (!$entite) {
            return;
        }
        
        $current = $entite;
        $depth = 0;
        
        while ($current && $current->parent_id && $depth < $maxDepth) {
            if (!$current->relationLoaded('parent')) {
                $current->load('parent');
            }
            if (!$current->parent) {
                break;
            }
            // Also load parent's parent chain
            if (!$current->parent->relationLoaded('parent') && $current->parent->parent_id) {
                $current->parent->load('parent');
            }
            $current = $current->parent;
            $depth++;
        }
    }

    /**
     * Get all child entity IDs recursively for a given parent entity ID.
     */
    public function getAllChildEntityIds($parentId, $maxDepth = 10)
    {
        $childIds = [];
        $this->collectChildIds($parentId, $childIds, $maxDepth, 0);
        return $childIds;
    }

    /**
     * Recursively collect all child entity IDs.
     */
    private function collectChildIds($parentId, &$childIds, $maxDepth, $depth)
    {
        if ($depth >= $maxDepth) {
            return;
        }
        
        $children = Entite::where('parent_id', $parentId)->get();
        
        foreach ($children as $child) {
            $childIds[] = $child->id;
            // Recursively get children of this child
            $this->collectChildIds($child->id, $childIds, $maxDepth, $depth + 1);
        }
    }

    /**
     * Check if an entity is a direction.
     * A direction is identified by:
     * 1. entity_type === 'direction'
     * 2. Name contains "DIRECTION" or "DIRECTIONS" (case insensitive)
     * 3. Name contains "DIRECTIONS REGIONALES" or "DIRECTION REGIONALE"
     */
    public function isDirection($entite): bool
    {
        if (!$entite) {
            return false;
        }
        
        // Check by entity_type
        if ($entite->entity_type === 'direction') {
            return true;
        }
        
        // Check by name patterns
        $nameUpper = strtoupper($entite->name ?? '');
        
        // Check for "DIRECTIONS REGIONALES" or "DIRECTION REGIONALE"
        if (
            strpos($nameUpper, 'DIRECTIONS REGIONALES') !== false ||
            strpos($nameUpper, 'DIRECTION REGIONALE') !== false
        ) {
            return true;
        }
        
        // Check for other direction patterns
        if (
            strpos($nameUpper, 'DIRECTION') !== false ||
            strpos($nameUpper, 'DIRECTIONS') !== false
        ) {
            // Exclude "Direction Provinciale" as they are under regional directions
            if (strpos($nameUpper, 'DIRECTION PROVINCIALE') === false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the direction entity for a given entity (traverse up parent hierarchy).
     * A direction is identified by:
     * 1. entity_type === 'direction'
     * 2. Name contains "DIRECTION" or "DIRECTIONS" (case insensitive)
     * 3. Name starts with "Direction" or "DIRECTION"
     * 4. Name contains "DIRECTIONS REGIONALES" or "DIRECTION REGIONALE"
     */
    public function getDirectionEntity($entite)
    {
        if (!$entite) {
            return null;
        }
        
        // Ensure parent is loaded
        if (!$entite->relationLoaded('parent') && $entite->parent_id) {
            $entite->load('parent');
        }
        
        // If this entity is a direction, return it
        if ($this->isDirection($entite)) {
            return $entite;
        }
        
        // Otherwise, traverse up the parent hierarchy
        $current = $entite;
        $maxDepth = 10; // Prevent infinite loops
        $depth = 0;
        
        while ($current && $current->parent_id && $depth < $maxDepth) {
            if (!$current->relationLoaded('parent')) {
                $current->load('parent');
            }
            if (!$current->parent) {
                break;
            }
            $current = $current->parent;
            // Load parent's parent if needed
            if ($current && $current->parent_id && !$current->relationLoaded('parent')) {
                $current->load('parent');
            }
            if ($current && $this->isDirection($current)) {
                return $current;
            }
            $depth++;
        }
        
        // If no direction found in hierarchy, return null
        return null;
    }

    /**
     * Check if a mutation is pending (not rejected and not fully approved).
     */
    public function isPending(Mutation $mutation): bool
    {
        // Check if mutation is rejected
        if ($mutation->rejected_by_super_rh || 
            $mutation->rejected_by_current_direction || 
            $mutation->rejected_by_destination_direction) {
            return false; // Rejected mutations are not pending
        }
        
        if ($mutation->mutation_type === 'interne') {
            // Internal: must be approved by current direction AND super Collaborateur Rh
            return !($mutation->approved_by_current_direction && $mutation->approved_by_super_collaborateur_rh);
        } elseif ($mutation->mutation_type === 'externe') {
            // External: must be approved by both directions AND super Collaborateur Rh
            // Also check if it's been sent to destination (still pending)
            return !($mutation->approved_by_current_direction && 
                    $mutation->sent_to_destination_by_super_rh &&
                    $mutation->approved_by_destination_direction && 
                    $mutation->approved_by_super_collaborateur_rh);
        } else {
            // Old mutations: check old validation fields
            return !($mutation->is_validated_ent && $mutation->valide_reception);
        }
    }


    /**
     * Validate mutation type (internal vs external).
     */
    public function validateMutationType(string $mutationType, int $toEntiteId, ?int $userCurrentEntiteId = null): array
    {
        if ($mutationType !== 'interne') {
            return ['valid' => true];
        }

        // For internal mutations, validate that it's allowed
        $toEntite = Entite::find($toEntiteId);
        if (!$toEntite) {
            return [
                'valid' => false,
                'error' => 'L\'entité de destination sélectionnée est invalide.'
            ];
        }

        // Get user's current entity
        $userCurrentEntite = $userCurrentEntiteId ? Entite::find($userCurrentEntiteId) : null;
        
        // Load parent chains
        $this->loadParentChain($toEntite);
        $toEntiteDirection = $this->getDirectionEntity($toEntite);
        $toEntiteDirectionId = $toEntiteDirection ? $toEntiteDirection->id : null;

        if ($userCurrentEntite) {
            $this->loadParentChain($userCurrentEntite);
        }
        $userDirection = $this->getDirectionEntity($userCurrentEntite);
        $userDirectionId = $userDirection ? $userDirection->id : null;

        // Check if it's a valid internal mutation
        $isValidInternal = false;

        // Standard case: same direction
        if ($userDirectionId && $toEntiteDirectionId && $userDirectionId === $toEntiteDirectionId) {
            $isValidInternal = true;
        }

        // Special case: user in special entity and destination is a child
        $specialEntityNames = \App\Helpers\EntityHelper::getSpecialEntityNames();
        if ($userCurrentEntite && in_array($userCurrentEntite->name, $specialEntityNames)) {
            $childEntityIds = $this->getAllChildEntityIds($userCurrentEntiteId);
            if (in_array($toEntite->id, $childEntityIds)) {
                $isValidInternal = true;
            }
        }

        if (!$isValidInternal) {
            return [
                'valid' => false,
                'error' => 'Cette mutation ne peut pas être de type interne. Les mutations internes sont uniquement autorisées entre entités de la même direction ou vers des services sous votre entité.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Get user's current entity from active parcours.
     */
    public function getUserCurrentEntite(string $ppr): ?Entite
    {
        $currentParcours = Parcours::where('ppr', $ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with('entite')
            ->orderBy('date_debut', 'desc')
            ->first();

        return $currentParcours ? $currentParcours->entite : null;
    }

    /**
     * Create a new mutation.
     */
    public function create(array $data): Mutation
    {
        return $this->mutationRepository->create($data);
    }

    /**
     * Update a mutation.
     */
    public function update(Mutation $mutation, array $data): Mutation
    {
        $this->mutationRepository->update($mutation->id, $data);
        return $mutation->fresh();
    }

    /**
     * Delete a mutation.
     */
    public function delete(Mutation $mutation): bool
    {
        return $this->mutationRepository->delete($mutation->id);
    }

    /**
     * Calculate mutation status based on approval/rejection flags.
     */
    public function calculateMutationStatus(Mutation $mutation): string
    {
        // Check if rejected
        if ($mutation->rejected_by_current_direction || 
            $mutation->rejected_by_destination_direction || 
            $mutation->rejected_by_super_rh) {
            return 'Rejeté';
        }

        // Check if fully approved
        if ($mutation->mutation_type === 'interne') {
            if ($mutation->approved_by_current_direction && 
                $mutation->approved_by_super_collaborateur_rh) {
                return 'Validé';
            }
            if ($mutation->approved_by_current_direction) {
                return 'En attente validation RH';
            }
        } elseif ($mutation->mutation_type === 'externe') {
            if ($mutation->approved_by_current_direction && 
                $mutation->approved_by_destination_direction && 
                $mutation->approved_by_super_collaborateur_rh) {
                return 'Validé';
            }
            if ($mutation->approved_by_current_direction && 
                $mutation->approved_by_destination_direction) {
                return 'En attente validation RH';
            }
            if ($mutation->approved_by_current_direction) {
                return 'En attente validation destination';
            }
        }

        return 'En attente';
    }

    /**
     * Transform mutation for frontend display.
     */
    public function transformMutationForFrontend(Mutation $mutation): array
    {
        $statut = $this->calculateMutationStatus($mutation);
        
        return [
            'id' => $mutation->id,
            'to_entite_name' => $mutation->toEntite ? $mutation->toEntite->name : 'N/A',
            'date_depot' => $mutation->created_at->toISOString(),
            'mutation_type' => $mutation->mutation_type,
            'statut' => $statut,
            'approved_by_current_direction' => $mutation->approved_by_current_direction,
            'approved_by_destination_direction' => $mutation->approved_by_destination_direction,
            'approved_by_super_collaborateur_rh' => $mutation->approved_by_super_collaborateur_rh,
            'rejected_by_current_direction' => $mutation->rejected_by_current_direction,
            'rejected_by_destination_direction' => $mutation->rejected_by_destination_direction,
            'rejected_by_super_rh' => $mutation->rejected_by_super_rh,
            'date_debut_affectation' => $mutation->date_debut_affectation ? $mutation->date_debut_affectation->toISOString() : null,
            'approved_by_current_direction_ppr' => $mutation->approved_by_current_direction_ppr,
            'approved_by_destination_direction_ppr' => $mutation->approved_by_destination_direction_ppr,
            'approved_by_super_collaborateur_rh_ppr' => $mutation->approved_by_super_collaborateur_rh_ppr,
            'approved_by_current_direction_at' => $mutation->approved_by_current_direction_at ? $mutation->approved_by_current_direction_at->toISOString() : null,
            'approved_by_destination_direction_at' => $mutation->approved_by_destination_direction_at ? $mutation->approved_by_destination_direction_at->toISOString() : null,
            'approved_by_super_collaborateur_rh_at' => $mutation->approved_by_super_collaborateur_rh_at ? $mutation->approved_by_super_collaborateur_rh_at->toISOString() : null,
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
    }

    /**
     * Get tracking statistics for a user.
     */
    public function getTrackingStatistics(string $ppr): array
    {
        return [
            'total' => $this->mutationRepository->where('ppr', $ppr)->count(),
            'pending' => $this->mutationRepository->countByPprAndStatus($ppr, 'pending'),
            'approved' => $this->mutationRepository->countByPprAndStatus($ppr, 'approved'),
            'rejected' => $this->mutationRepository->countByPprAndStatus($ppr, 'rejected'),
        ];
    }

    /**
     * Get all mutations for tracking page (last 10 years).
     */
    public function getAllMutationsForTracking(string $ppr, array $relations = []): Collection
    {
        return $this->mutationRepository->getByPprForLastYears($ppr, 10, $relations);
    }

    /**
     * Check if user has pending mutations.
     */
    public function hasPendingMutations(string $ppr): bool
    {
        return $this->mutationRepository->hasPendingMutations($ppr);
    }

    /**
     * Get pending mutations for a user.
     */
    public function getPendingMutations(string $ppr): Collection
    {
        return $this->mutationRepository->getByPpr($ppr)
            ->filter(function($mutation) {
                if ($mutation->mutation_type === 'interne') {
                    return !$mutation->approved_by_current_direction && 
                           !$mutation->rejected_by_current_direction;
                } elseif ($mutation->mutation_type === 'externe') {
                    return (!$mutation->approved_by_current_direction || 
                            !$mutation->approved_by_destination_direction) &&
                           !$mutation->rejected_by_current_direction &&
                           !$mutation->rejected_by_destination_direction &&
                           !$mutation->rejected_by_super_rh;
                }
                return false;
            });
    }

    /**
     * Check if mutation can be deleted by user.
     */
    public function canDelete(Mutation $mutation, string $ppr): array
    {
        // Verify that the mutation belongs to the current user
        if ($mutation->ppr !== $ppr) {
            return [
                'can_delete' => false,
                'error' => 'Vous n\'avez pas l\'autorisation de supprimer cette demande.'
            ];
        }

        // Check if mutation can be deleted (must be pending, not approved or rejected)
        $canDelete = false;

        if ($mutation->mutation_type === 'interne') {
            // Internal mutations: can delete if not approved or rejected by current direction
            $canDelete = !$mutation->approved_by_current_direction && 
                        !$mutation->rejected_by_current_direction;
        } elseif ($mutation->mutation_type === 'externe') {
            // External mutations: can delete if not approved or rejected by either direction
            $canDelete = !$mutation->approved_by_current_direction && 
                        !$mutation->approved_by_destination_direction &&
                        !$mutation->rejected_by_current_direction &&
                        !$mutation->rejected_by_destination_direction;
        } else {
            // Fallback: can delete if not validated
            $canDelete = !$mutation->is_validated_ent && !$mutation->valide_reception;
        }

        if (!$canDelete) {
            return [
                'can_delete' => false,
                'error' => 'Vous ne pouvez supprimer que les demandes en attente qui n\'ont pas encore été approuvées ou rejetées.'
            ];
        }

        return ['can_delete' => true];
    }

    /**
     * Check if user is director of a direction.
     */
    public function isDirectorOfDirection(User $user): bool
    {
        return Entite::where('chef_ppr', $user->ppr)
            ->where('entity_type', 'direction')
            ->exists();
    }

    /**
     * Check if user is chef of one of the specific entities that can manage mutations.
     */
    public function isChefOfSpecialEntity(User $user): bool
    {
        $specialEntityNames = \App\Helpers\EntityHelper::getSpecialEntityNames();

        return Entite::where('chef_ppr', $user->ppr)
            ->whereIn('name', $specialEntityNames)
            ->exists();
    }

    /**
     * Get entities where user is chef (for special entities that can manage mutations).
     */
    public function getChefSpecialEntities(User $user)
    {
        $specialEntityNames = \App\Helpers\EntityHelper::getSpecialEntityNames();

        return Entite::where('chef_ppr', $user->ppr)
            ->whereIn('name', $specialEntityNames)
            ->get();
    }

    /**
     * Get directions where user is director.
     */
    public function getDirectorDirections(User $user)
    {
        return Entite::where('chef_ppr', $user->ppr)
            ->where('entity_type', 'direction')
            ->get();
    }

    /**
     * Get agent PPRs in a direction.
     */
    public function getAgentPprsInDirection($directionId)
    {
        // Get all entities in this direction (including the direction itself and all children)
        $direction = Entite::find($directionId);
        if (!$direction) {
            return [];
        }

        // Get all descendant entities
        $allEntiteIds = [$directionId];
        $this->getDescendantEntiteIds($direction, $allEntiteIds);

        // Get all active parcours in these entities
        $pprs = Parcours::whereIn('entite_id', $allEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->pluck('ppr')
            ->unique()
            ->toArray();

        return $pprs;
    }

    /**
     * Recursively get all descendant entity IDs.
     */
    private function getDescendantEntiteIds($entite, &$ids)
    {
        $children = Entite::where('parent_id', $entite->id)->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            $this->getDescendantEntiteIds($child, $ids);
        }
    }

    /**
     * Get all descendant entity IDs for a given entity.
     */
    public function getDescendantEntiteIdsForEntity($entite): array
    {
        $ids = [];
        $this->getDescendantEntiteIds($entite, $ids);
        return $ids;
    }

    /**
     * Update parcours for approved mutation.
     */
    public function updateParcoursForApprovedMutation(Mutation $mutation, Parcours $userParcours)
    {
        // Check if parcours already updated for this mutation (avoid duplicate updates)
        // Check if there's already a parcours for the destination entity starting from approval date
        $approvalDate = $mutation->approved_by_current_direction_at ?? 
                       $mutation->approved_by_destination_direction_at ?? 
                       now();
        $approvalDateStart = Carbon::parse($approvalDate)->startOfDay();
        
        $existingParcours = Parcours::where('ppr', $mutation->ppr)
            ->where('entite_id', $mutation->to_entite_id)
            ->where('date_debut', '>=', $approvalDateStart->copy()->subDays(1))
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->first();
        
        // If parcours already exists for this mutation, skip update
        if ($existingParcours) {
            return;
        }
        
        // Use transaction to ensure atomicity
        DB::transaction(function() use ($mutation, $userParcours, $approvalDateStart) {
            $today = $approvalDateStart->copy();
            
            // Close the current parcours by setting date_fin to the day before new one starts
            $endDate = $today->copy()->subDay();
            
            // Only update if the parcours is still active (date_fin is null or in the future)
            if (is_null($userParcours->date_fin) || $userParcours->date_fin >= now()) {
                $userParcours->date_fin = $endDate;
                $userParcours->save();
            }

            // Create new parcours entry for the destination entity
            Parcours::create([
                'ppr' => $mutation->ppr,
                'entite_id' => $mutation->to_entite_id,
                'poste' => $userParcours->poste ?? 'Agent', // Keep same poste or default
                'date_debut' => $today,
                'date_fin' => null, // Active parcours
                'grade_id' => $userParcours->grade_id, // Preserve grade
                'reason' => $mutation->motif ?? 'Mutation approuvée', // Use mutation motif as reason
            ]);
        });
    }

    /**
     * Update parcours for super RH validation.
     */
    public function updateParcoursForSuperRhValidation(Mutation $mutation, Parcours $userParcours)
    {
        // Use date_debut_affectation if set, otherwise use approval date
        $approvalDate = $mutation->date_debut_affectation ?? $mutation->approved_by_super_collaborateur_rh_at ?? now();
        $approvalDateStart = Carbon::parse($approvalDate)->startOfDay();
        
        $existingParcours = Parcours::where('ppr', $mutation->ppr)
            ->where('entite_id', $mutation->to_entite_id)
            ->where('date_debut', '>=', $approvalDateStart->copy()->subDays(1))
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->first();
        
        if ($existingParcours) {
            return;
        }
        
        DB::transaction(function() use ($mutation, $userParcours, $approvalDateStart) {
            $today = $approvalDateStart->copy();
            $endDate = $today->copy()->subDay();
            
            if (is_null($userParcours->date_fin) || $userParcours->date_fin >= now()) {
                $userParcours->date_fin = $endDate;
                $userParcours->save();
            }

            Parcours::create([
                'ppr' => $mutation->ppr,
                'entite_id' => $mutation->to_entite_id,
                'poste' => $userParcours->poste ?? 'Agent',
                'date_debut' => $today,
                'date_fin' => null,
                'grade_id' => $userParcours->grade_id,
                'reason' => $mutation->motif ?? 'Mutation validée par Super Collaborateur RH',
            ]);
        });
    }

    /**
     * Process motif (handle "Autre" case).
     */
    public function processMotif(string $motif, ?string $motifAutre = null): string
    {
        if ($motif === 'Autre' && !empty($motifAutre)) {
            return $motifAutre;
        }
        return $motif;
    }

    /**
     * Validate motif (check if "Autre" requires motif_autre).
     */
    public function validateMotif(string $motif, ?string $motifAutre = null): array
    {
        if ($motif === 'Autre' && empty($motifAutre)) {
            return [
                'valid' => false,
                'error' => 'Veuillez préciser le motif lorsque vous sélectionnez "Autre".'
            ];
        }
        return ['valid' => true];
    }

    /**
     * Approve mutation by current or destination direction.
     */
    public function approveMutation(Mutation $mutation, User $approver, string $approvalType): Mutation
    {
        if ($approvalType === 'current') {
            $mutation->approved_by_current_direction = true;
            $mutation->approved_by_current_direction_ppr = $approver->ppr;
            $mutation->approved_by_current_direction_at = now();
        } elseif ($approvalType === 'destination') {
            $mutation->approved_by_destination_direction = true;
            $mutation->approved_by_destination_direction_ppr = $approver->ppr;
            $mutation->approved_by_destination_direction_at = now();
        }

        $mutation->save();
        return $mutation->fresh();
    }

    /**
     * Reject mutation by current or destination direction.
     */
    public function rejectMutation(Mutation $mutation, User $rejector, string $rejectionType, string $reason = ''): Mutation
    {
        if ($rejectionType === 'current') {
            $mutation->rejected_by_current_direction = true;
            $mutation->rejected_by_current_direction_ppr = $rejector->ppr;
            $mutation->rejection_reason_current = $reason;
            $mutation->rejected_by_current_direction_at = now();
        } elseif ($rejectionType === 'destination') {
            $mutation->rejected_by_destination_direction = true;
            $mutation->rejected_by_destination_direction_ppr = $rejector->ppr;
            $mutation->rejection_reason_destination = $reason;
            $mutation->rejected_by_destination_direction_at = now();
        }

        $mutation->save();
        return $mutation->fresh();
    }

    /**
     * Approve destination reception by super RH (initial review or final validation).
     */
    public function approveDestinationReception(Mutation $mutation, User $approver, ?string $dateDebutAffectation = null): Mutation
    {
        if ($mutation->mutation_type === 'externe') {
            if ($mutation->sent_to_destination_by_super_rh && 
                $mutation->approved_by_destination_direction && 
                !$mutation->rejected_by_destination_direction) {
                // Final validation
                $mutation->approved_by_super_collaborateur_rh = true;
                $mutation->approved_by_super_collaborateur_rh_ppr = $approver->ppr;
                $mutation->approved_by_super_collaborateur_rh_at = now();
                if ($dateDebutAffectation) {
                    $mutation->date_debut_affectation = $dateDebutAffectation;
                }
            } else {
                // Initial review: send to destination
                $mutation->sent_to_destination_by_super_rh = true;
                $mutation->sent_to_destination_by_super_rh_ppr = $approver->ppr;
                $mutation->sent_to_destination_by_super_rh_at = now();
            }
        } elseif ($mutation->mutation_type === 'interne') {
            // Final validation
            $mutation->approved_by_super_collaborateur_rh = true;
            $mutation->approved_by_super_collaborateur_rh_ppr = $approver->ppr;
            $mutation->approved_by_super_collaborateur_rh_at = now();
            if ($dateDebutAffectation) {
                $mutation->date_debut_affectation = $dateDebutAffectation;
            }
        }

        $mutation->save();
        return $mutation->fresh();
    }

    /**
     * Reject destination reception by super RH.
     */
    public function rejectDestinationReception(Mutation $mutation, User $rejector, string $reason): Mutation
    {
        $mutation->rejected_by_super_rh = true;
        $mutation->rejected_by_super_rh_ppr = $rejector->ppr;
        $mutation->rejection_reason_super_rh = $reason;
        $mutation->rejected_by_super_rh_at = now();
        $mutation->save();

        return $mutation->fresh();
    }

    /**
     * Check if mutation can be approved by user (authorization check).
     */
    public function canApproveMutation(Mutation $mutation, User $user, string $approvalType): array
    {
        $userParcours = Parcours::where('ppr', $mutation->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                    ->orWhere('date_fin', '>=', now());
            })
            ->with('entite')
            ->orderBy('date_debut', 'desc')
            ->first();

        $userCurrentEntite = $userParcours ? $userParcours->entite : null;
        $userCurrentDirection = $userCurrentEntite ? $this->getDirectionEntity($userCurrentEntite) : null;
        $userCurrentDirectionId = $userCurrentDirection ? $userCurrentDirection->id : null;

        $destinationEntite = $mutation->toEntite;
        $destinationDirection = $destinationEntite ? $this->getDirectionEntity($destinationEntite) : null;
        $destinationDirectionId = $destinationDirection ? $destinationDirection->id : null;

        $directorDirections = $this->getDirectorDirections($user);
        $directionIds = $directorDirections->pluck('id')->toArray();

        $specialEntities = $this->getChefSpecialEntities($user);
        $specialEntityIds = $specialEntities->pluck('id')->toArray();

        if ($approvalType === 'current') {
            $canApprove = false;
            if ($userCurrentDirectionId && in_array($userCurrentDirectionId, $directionIds)) {
                $canApprove = true;
            } elseif ($userCurrentEntite && in_array($userCurrentEntite->id, $specialEntityIds)) {
                $canApprove = true;
            }

            if (!$canApprove) {
                return [
                    'can_approve' => false,
                    'error' => 'You are not authorized to approve this request from the current direction/entity.'
                ];
            }
        } elseif ($approvalType === 'destination') {
            $canApprove = false;
            if ($destinationDirectionId && in_array($destinationDirectionId, $directionIds)) {
                $canApprove = true;
            } elseif ($destinationEntite && in_array($destinationEntite->id, $specialEntityIds)) {
                $canApprove = true;
            }

            if (!$canApprove) {
                return [
                    'can_approve' => false,
                    'error' => 'You are not authorized to approve this request from the destination direction/entity.'
                ];
            }
        }

        return ['can_approve' => true];
    }

    /**
     * Check if mutation can be rejected by user (authorization check).
     */
    public function canRejectMutation(Mutation $mutation, User $user, string $rejectionType): array
    {
        // Same logic as canApproveMutation
        return $this->canApproveMutation($mutation, $user, $rejectionType);
    }
}


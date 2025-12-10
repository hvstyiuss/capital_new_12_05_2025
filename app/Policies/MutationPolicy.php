<?php

namespace App\Policies;

use App\Models\Mutation;
use App\Models\User;
use App\Models\Entite;
use App\Models\Parcours;
use App\Services\MutationService;

class MutationPolicy
{
    protected function mutationService(): MutationService
    {
        return app(MutationService::class);
    }

    /**
     * Any authenticated user can list mutations (filtered by ownership in controller).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * A user can view a mutation if it is theirs, they are admin/HR, or they manage it as director/chef.
     */
    public function view(User $user, Mutation $mutation): bool
    {
        if ($mutation->ppr === $user->ppr) {
            return true;
        }

        if ($user->hasRole('admin') || 
            $user->hasRole('Collaborateur Rh') || 
            $user->hasRole('super Collaborateur Rh')) {
            return true;
        }

        // Directors and special entity chefs can view mutations they manage
        return $this->canManageMutation($user, $mutation);
    }

    /**
     * Any authenticated user can create their own mutation request.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the owner (before processing) or HR/admin can update.
     */
    public function update(User $user, Mutation $mutation): bool
    {
        if ($mutation->ppr === $user->ppr) {
            // Owner can only update if pending
            return $this->mutationService()->isPending($mutation);
        }

        return $user->hasRole('admin')
            || $user->hasRole('Collaborateur Rh')
            || $user->hasRole('super Collaborateur Rh');
    }

    /**
     * Only admin or the owner (while pending) can delete.
     */
    public function delete(User $user, Mutation $mutation): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($mutation->ppr !== $user->ppr) {
            return false;
        }

        return $this->mutationService()->isPending($mutation);
    }

    /**
     * Directors of directions or chefs of special entities can approve/reject mutations.
     */
    public function approveAsDirection(User $user, Mutation $mutation): bool
    {
        if (!$this->mutationService()->isDirectorOfDirection($user) && 
            !$this->mutationService()->isChefOfSpecialEntity($user)) {
            return false;
        }

        // Get mutation context
        $userParcours = Parcours::where('ppr', $mutation->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with('entite')
            ->orderBy('date_debut', 'desc')
            ->first();

        $userCurrentEntite = $userParcours ? $userParcours->entite : null;
        $userCurrentDirection = $userCurrentEntite ? $this->mutationService()->getDirectionEntity($userCurrentEntite) : null;
        $userCurrentDirectionId = $userCurrentDirection ? $userCurrentDirection->id : null;

        $destinationEntite = $mutation->toEntite;
        $destinationDirection = $destinationEntite ? $this->mutationService()->getDirectionEntity($destinationEntite) : null;
        $destinationDirectionId = $destinationDirection ? $destinationDirection->id : null;

        $directorDirections = $this->mutationService->getDirectorDirections($user);
        $directionIds = $directorDirections->pluck('id')->toArray();
        
        $specialEntities = $this->mutationService->getChefSpecialEntities($user);
        $specialEntityIds = $specialEntities->pluck('id')->toArray();

        // Check if user can approve current direction
        if ($userCurrentDirectionId && in_array($userCurrentDirectionId, $directionIds)) {
            return true;
        }
        if ($userCurrentEntite && in_array($userCurrentEntite->id, $specialEntityIds)) {
            return true;
        }

        // Check if user can approve destination direction (for external mutations)
        if ($mutation->mutation_type === 'externe') {
            if ($destinationDirectionId && in_array($destinationDirectionId, $directionIds)) {
                return true;
            }
            if ($destinationEntite && in_array($destinationEntite->id, $specialEntityIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Directors of directions or chefs of special entities can access agent mutation requests.
     */
    public function manageAsDirection(User $user): bool
    {
        return $this->mutationService()->isDirectorOfDirection($user)
            || $this->mutationService()->isChefOfSpecialEntity($user);
    }

    /**
     * Super Collaborateur Rh can manage all mutations.
     */
    public function superRh(User $user): bool
    {
        return $user->hasRole('super Collaborateur Rh');
    }

    /**
     * Check if user can manage a specific mutation (as director or chef).
     */
    protected function canManageMutation(User $user, Mutation $mutation): bool
    {
        if (!$this->manageAsDirection($user)) {
            return false;
        }

        // Get agent PPRs in user's directions
        $directorDirections = $this->mutationService->getDirectorDirections($user);
        $directionIds = $directorDirections->pluck('id')->toArray();
        
        $specialEntities = $this->mutationService->getChefSpecialEntities($user);
        $specialEntityIds = $specialEntities->pluck('id')->toArray();

        $agentPprs = [];
        foreach ($directionIds as $directionId) {
            $agentPprs = array_merge($agentPprs, $this->mutationService()->getAgentPprsInDirection($directionId));
        }
        foreach ($specialEntityIds as $specialEntityId) {
            $entityPprs = Parcours::where('entite_id', $specialEntityId)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->pluck('ppr')
                ->unique()
                ->toArray();
            $agentPprs = array_merge($agentPprs, $entityPprs);
        }
        $agentPprs = array_unique($agentPprs);

        // Check if mutation belongs to an agent in user's direction
        return in_array($mutation->ppr, $agentPprs);
    }
}



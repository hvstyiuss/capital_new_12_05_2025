<?php

namespace App\Actions\Mutation;

use App\Models\Entite;
use App\Models\User;
use App\Services\MutationService;

class PrepareCreateFormAction
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    public function execute(User $user): array
    {
        // Get all entities for selection
        $entites = Entite::orderBy('name')->get();

        // Determine user's current entity and direction
        $userCurrentEntite = $this->mutationService->getUserCurrentEntite($user->ppr);
        $userDirection = $this->mutationService->getDirectionEntity($userCurrentEntite);
        $userDirectionId = $userDirection ? $userDirection->id : null;

        // Build auxiliary data for each entity (used by the view JS)
        $entitesData = $entites->map(function (Entite $entite) use ($userCurrentEntite, $userDirectionId) {
            // Load parent chain so getDirectionEntity works correctly
            $this->mutationService->loadParentChain($entite);
            $direction = $this->mutationService->getDirectionEntity($entite);
            $directionId = $direction ? $direction->id : null;

            $isInternal = $userDirectionId && $directionId && $userDirectionId === $directionId;
            $isCurrentEntity = $userCurrentEntite && $userCurrentEntite->id === $entite->id;

            // People count in this entity (if relation exists)
            $peopleCount = 0;
            if (method_exists($entite, 'users')) {
                $peopleCount = $entite->users()
                    ->where('is_active', true)
                    ->where('is_deleted', false)
                    ->count();
            }

            return [
                'id' => $entite->id,
                'is_internal' => $isInternal,
                'is_current_entity' => $isCurrentEntity,
                'people_count' => $peopleCount,
            ];
        });

        // Count internal entities (excluding current entity)
        $internalEntitiesCount = $entitesData->filter(function ($data) {
            return $data['is_internal'] && !$data['is_current_entity'];
        })->count();

        return [
            'entites' => $entites,
            'entitesData' => $entitesData,
            'hasInternalEntities' => $internalEntitiesCount > 0,
        ];
    }
}





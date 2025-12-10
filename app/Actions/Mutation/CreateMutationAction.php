<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\Models\User;
use App\DTOs\Mutation\CreateMutationDTO;
use App\Services\MutationService;
use DomainException;

class CreateMutationAction
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    /**
     * Create a new mutation from DTO and the authenticated user.
     *
     * @throws DomainException when business rules are violated
     */
    public function execute(User $user, \App\DTOs\Mutation\CreateMutationDTO $dto): Mutation
    {
        // Check if user already has a pending mutation
        $pendingMutations = $this->mutationService->getPendingMutations($user->ppr);
        if ($pendingMutations->isNotEmpty()) {
            throw new DomainException('Vous avez déjà une demande de mutation en attente.');
        }

        // Validate and process motif
        $motif = $dto->motif;
        if ($motif === 'Autre') {
            if (empty($dto->motifAutre)) {
                throw new DomainException('Veuillez préciser le motif lorsque vous sélectionnez "Autre".');
            }
            $motif = $dto->motifAutre;
        }

        // Get user's current entity
        $userCurrentEntite = $this->mutationService->getUserCurrentEntite($user->ppr);
        $userCurrentEntiteId = $userCurrentEntite ? $userCurrentEntite->id : null;

        // Validate mutation type
        $validation = $this->mutationService->validateMutationType(
            $dto->mutationType,
            $dto->toEntiteId,
            $userCurrentEntiteId
        );

        if (!$validation['valid']) {
            throw new DomainException($validation['error']);
        }

        // Prepare data for creation
        $data = [
            'ppr' => $user->ppr,
            'to_entite_id' => $dto->toEntiteId,
            'mutation_type' => $dto->mutationType,
            'motif' => $motif,
            'is_validated_ent' => false,
            'valide_reception' => false,
            'approved_by_current_direction' => false,
            'approved_by_destination_direction' => false,
            'approved_by_super_collaborateur_rh' => false,
        ];

        return $this->mutationService->create($data);
    }
}




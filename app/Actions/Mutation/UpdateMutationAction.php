<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\DTOs\Mutation\UpdateMutationDTO;
use App\Services\MutationService;

class UpdateMutationAction
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    /**
     * Update an existing mutation from DTO.
     */
    public function execute(Mutation $mutation, UpdateMutationDTO $dto): Mutation
    {
        $data = array_filter([
            'to_entite_id' => $dto->toEntiteId,
            'mutation_type' => $dto->mutationType,
            'motif' => $dto->motif,
            'motif_autre' => $dto->motifAutre,
            'approved_by_current_direction' => $dto->approvedByCurrentDirection,
            'approved_by_destination_direction' => $dto->approvedByDestinationDirection,
            'approved_by_super_collaborateur_rh' => $dto->approvedBySuperCollaborateurRh,
            'rejected_by_current_direction' => $dto->rejectedByCurrentDirection,
            'rejected_by_destination_direction' => $dto->rejectedByDestinationDirection,
            'rejected_by_super_rh' => $dto->rejectedBySuperRh,
            'rejection_reason_current' => $dto->rejectionReasonCurrent,
            'rejection_reason_destination' => $dto->rejectionReasonDestination,
            'rejection_reason_super_rh' => $dto->rejectionReasonSuperRh,
            'date_debut_affectation' => $dto->dateDebutAffectation,
        ], fn($value) => $value !== null);

        return $this->mutationService->update($mutation, $data);
    }
}






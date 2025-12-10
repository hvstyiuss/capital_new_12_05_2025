<?php

namespace App\DTOs\Mutation;

readonly class UpdateMutationDTO
{
    public function __construct(
        public ?int $toEntiteId,
        public ?string $mutationType,
        public ?string $motif,
        public ?string $motifAutre,
        public ?bool $approvedByCurrentDirection,
        public ?bool $approvedByDestinationDirection,
        public ?bool $approvedBySuperCollaborateurRh,
        public ?bool $rejectedByCurrentDirection,
        public ?bool $rejectedByDestinationDirection,
        public ?bool $rejectedBySuperRh,
        public ?string $rejectionReasonCurrent,
        public ?string $rejectionReasonDestination,
        public ?string $rejectionReasonSuperRh,
        public ?string $dateDebutAffectation,
    ) {}
}





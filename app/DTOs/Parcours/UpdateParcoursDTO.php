<?php

namespace App\DTOs\Parcours;

readonly class UpdateParcoursDTO
{
    public function __construct(
        public ?string $ppr,
        public ?int $entiteId,
        public ?string $poste,
        public ?string $role,
        public ?string $dateDebut,
        public ?string $dateFin,
        public ?int $gradeId,
        public ?string $reason,
        public ?string $createdByPpr,
    ) {}
}





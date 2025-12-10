<?php

namespace App\DTOs\Conge;

readonly class UpdateCongeDTO
{
    public function __construct(
        public ?int $annee,
    ) {}
}





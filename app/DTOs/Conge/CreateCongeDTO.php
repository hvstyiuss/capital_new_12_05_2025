<?php

namespace App\DTOs\Conge;

readonly class CreateCongeDTO
{
    public function __construct(
        public string $ppr,
        public int $annee,
    ) {}
}





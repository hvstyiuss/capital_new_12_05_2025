<?php

namespace App\DTOs\Annonce;

use Illuminate\Http\UploadedFile;

readonly class CreateAnnonceDTO
{
    public function __construct(
        public ?string $ppr,
        public string $content,
        public int $typeAnnonceId,
        public ?string $statut,
        public ?UploadedFile $image,
        public array $entites, // Array of entity IDs
    ) {}
}





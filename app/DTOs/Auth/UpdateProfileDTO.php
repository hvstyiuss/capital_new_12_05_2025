<?php

namespace App\DTOs\Auth;

use Illuminate\Http\UploadedFile;

readonly class UpdateProfileDTO
{
    public function __construct(
        public string $name,
        public ?string $currentPassword,
        public ?string $newPassword,
        public ?UploadedFile $image,
    ) {}
}





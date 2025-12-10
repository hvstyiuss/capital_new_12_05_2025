<?php

namespace App\DTOs\User;

readonly class UpdateUserDTO
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $image,
        public ?bool $isActive,
        public ?string $password,
    ) {}
}





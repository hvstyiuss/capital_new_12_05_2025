<?php

namespace App\DTOs\User;

readonly class CreateUserDTO
{
    public function __construct(
        public string $ppr,
        public string $name,
        public string $password,
        public ?string $email,
        public ?string $image,
        public ?bool $isActive,
    ) {}
}





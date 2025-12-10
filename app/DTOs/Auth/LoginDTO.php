<?php

namespace App\DTOs\Auth;

readonly class LoginDTO
{
    public function __construct(
        public string $ppr,
        public string $password,
        public int $captcha,
        public bool $remember,
        public string $ipAddress,
    ) {}
}





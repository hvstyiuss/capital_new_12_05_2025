<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Services\AuthService;

class LoginAction
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Execute the login workflow using DTO.
     */
    public function execute(LoginDTO $dto): array
    {
        return $this->authService->attemptLoginWithData(
            $dto->ppr,
            $dto->password,
            $dto->captcha,
            $dto->remember,
            $dto->ipAddress
        );
    }
}



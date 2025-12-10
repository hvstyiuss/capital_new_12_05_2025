<?php

namespace App\Actions\Auth;

use App\Services\AuthService;

class RefreshCaptchaAction
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Regenerate the captcha and return its data.
     */
    public function execute(): array
    {
        return $this->authService->regenerateCaptcha();
    }
}






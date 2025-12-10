<?php

namespace App\Actions\Auth;

use App\Services\AuthService;

class ShowLoginAction
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Prepare data required for the login page (captcha, block state).
     */
    public function execute(string $ipAddress): array
    {
        $captcha = $this->authService->prepareCaptcha();
        $block   = $this->authService->getBlockState($ipAddress);

        return [
            'captcha_question'  => $captcha['captcha_question'],
            'captcha_answer'    => $captcha['captcha_answer'],
            'isBlocked'         => $block['isBlocked'],
            'remainingMinutes'  => $block['remainingMinutes'],
            'remainingSecs'     => $block['remainingSecs'],
        ];
    }
}






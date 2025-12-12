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

        // Format remaining time message
        $remainingTimeMessage = $this->formatRemainingTime(
            $block['remainingMinutes'] ?? 0,
            $block['remainingSecs'] ?? 0
        );

        return [
            'captcha_question'  => $captcha['captcha_question'],
            'captcha_answer'    => $captcha['captcha_answer'],
            'isBlocked'         => $block['isBlocked'],
            'remainingMinutes'  => $block['remainingMinutes'],
            'remainingSecs'     => $block['remainingSecs'],
            'remainingTimeMessage' => $remainingTimeMessage,
        ];
    }

    /**
     * Format remaining time in a human-readable French message.
     */
    private function formatRemainingTime(int $remainingMinutes, int $remainingSecs): string
    {
        $timeParts = [];
        if ($remainingMinutes > 0) {
            $timeParts[] = $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
        }
        if ($remainingSecs > 0) {
            $timeParts[] = $remainingSecs . ' seconde' . ($remainingSecs > 1 ? 's' : '');
        }
        if (empty($timeParts)) {
            $timeParts[] = 'quelques secondes';
        }
        return implode(' et ', $timeParts);
    }
}






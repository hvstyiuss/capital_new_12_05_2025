<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Generate or reuse captcha for the current session.
     */
    public function prepareCaptcha(): array
    {
        $sessionAnswer = session('captcha_answer');
        $sessionQuestion = session('captcha_question');

        if ($sessionAnswer === null || $sessionQuestion === null) {
            $num1 = rand(1, 5);
            $num2 = rand(1, 5);
            $captchaQuestion = "{$num1} + {$num2}";
            $captchaAnswer   = $num1 + $num2;

            session([
                'captcha_question' => $captchaQuestion,
                'captcha_answer'   => (int) $captchaAnswer,
            ]);
            session()->save();
        } else {
            $captchaQuestion = $sessionQuestion;
            $captchaAnswer   = $sessionAnswer;
        }

        return [
            'captcha_question' => $captchaQuestion,
            'captcha_answer'   => $captchaAnswer,
        ];
    }

    /**
     * Check if login is blocked for an IP and return remaining time (in minutes, seconds).
     */
    public function getBlockState(string $ipAddress): array
    {
        $blockedUntil = Cache::get("login_blocked_{$ipAddress}");
        $isBlocked    = $blockedUntil && now()->lt($blockedUntil);

        if ($isBlocked) {
            $remainingSeconds = now()->diffInSeconds($blockedUntil);
            $remainingMinutes = floor($remainingSeconds / 60);
            $remainingSecs    = $remainingSeconds % 60;
        } else {
            $remainingMinutes = 0;
            $remainingSecs    = 0;
        }

        return [
            'isBlocked'        => $isBlocked,
            'remainingMinutes' => $remainingMinutes,
            'remainingSecs'    => $remainingSecs,
        ];
    }

    /**
     * Generate a brand new captcha (used on failed attempts / refresh).
     */
    public function regenerateCaptcha(): array
    {
        $num1 = rand(1, 5);
        $num2 = rand(1, 5);
        $captchaQuestion = "{$num1} + {$num2}";
        $captchaAnswer   = $num1 + $num2;

        session([
            'captcha_question' => $captchaQuestion,
            'captcha_answer'   => (int) $captchaAnswer,
        ]);
        session()->save();

        return [
            'captcha_question' => $captchaQuestion,
            'captcha_answer'   => $captchaAnswer,
        ];
    }

    /**
     * Validate captcha answer from request-independent data against session.
     */
    public function validateCaptcha(int $userAnswer): bool
    {
        $sessionAnswer = session('captcha_answer');

        $sessionAnswerInt = is_numeric($sessionAnswer) ? (int) $sessionAnswer : null;
        $userAnswerInt    = is_numeric($userAnswer) ? (int) $userAnswer : null;

        Log::info('CAPTCHA Validation', [
            'session_answer_raw' => $sessionAnswer,
            'session_answer_int' => $sessionAnswerInt,
            'user_answer_raw'    => $userAnswer,
            'user_answer_int'    => $userAnswerInt,
            'match'              => $sessionAnswerInt !== null && $sessionAnswerInt === $userAnswerInt,
            'session_id'         => session()->getId(),
        ]);

        if ($sessionAnswerInt === null || $userAnswerInt === null) {
            Log::warning('CAPTCHA Validation Failed - null values', [
                'session_answer'     => $sessionAnswer,
                'session_answer_int' => $sessionAnswerInt,
                'user_answer'        => $userAnswer,
                'user_answer_int'    => $userAnswerInt,
                'session_id'         => session()->getId(),
            ]);

            return false;
        }

        return $sessionAnswerInt === $userAnswerInt;
    }

    /**
     * Handle the full login workflow: captcha, rate limiting, user verification.
     * This variant is framework-agnostic and can be used from Actions.
     */
    public function attemptLoginWithData(
        string $ppr,
        string $password,
        int $captcha,
        bool $remember,
        string $ipAddress
    ): array
    {
        // Check rate limit
        $blockedUntil = Cache::get("login_blocked_{$ipAddress}");
        if ($blockedUntil && now()->lt($blockedUntil)) {
            return [
                'success'  => false,
                'blocked'  => true,
                'user'     => null,
                'captcha'  => $this->regenerateCaptcha(),
                'message'  => 'Les tentatives de connexion sont temporairement bloquées pour cette adresse IP.',
            ];
        }

        // Validate captcha first
        if (!$this->validateCaptcha($captcha)) {
            $this->incrementFailedAttempts($ipAddress);

            return [
                'success' => false,
                'blocked' => false,
                'user'    => null,
                'captcha' => $this->regenerateCaptcha(),
                'errors'  => ['captcha' => 'La réponse à la question de sécurité est incorrecte.'],
            ];
        }

        /** @var User|null $user */
        $user = $this->authRepository->findByPpr($ppr);

        if (!$user || !Hash::check($password, $user->password)) {
            $this->incrementFailedAttempts($ipAddress);

            return [
                'success' => false,
                'blocked' => false,
                'user'    => null,
                'captcha' => $this->regenerateCaptcha(),
                'errors'  => ['ppr' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'],
            ];
        }

        if (!$user->is_active || $user->is_deleted) {
            return [
                'success' => false,
                'blocked' => false,
                'user'    => null,
                'captcha' => $this->regenerateCaptcha(),
                'errors'  => ['ppr' => 'Votre compte est inactif.'],
            ];
        }

        // Success: clear attempts and log in
        $this->clearFailedAttempts($ipAddress);
        Auth::login($user, $remember);
        session()->regenerate();
        session()->forget(['captcha_answer', 'captcha_question']);

        return [
            'success' => true,
            'blocked' => false,
            'user'    => $user,
            'captcha' => null,
        ];
    }

    /**
     * Backwards-compatible wrapper used where a Request is still available.
     */
    public function attemptLogin(\Illuminate\Http\Request $request): array
    {
        $data = $request->only(['ppr', 'password', 'captcha', 'remember']);

        return $this->attemptLoginWithData(
            $data['ppr'],
            $data['password'],
            (int) $data['captcha'],
            (bool) ($data['remember'] ?? false),
            $request->ip()
        );
    }

    /**
     * Increment failed login attempts.
     */
    protected function incrementFailedAttempts(string $ipAddress): void
    {
        $attemptsKey = "login_attempts_{$ipAddress}";
        $attempts    = Cache::get($attemptsKey, 0);
        $attempts++;

        Cache::put($attemptsKey, $attempts, now()->addMinutes(20));

        if ($attempts >= 3) {
            $blockedUntil = now()->addMinutes(20);
            Cache::put("login_blocked_{$ipAddress}", $blockedUntil, now()->addMinutes(20));
        }
    }

    /**
     * Clear failed login attempts.
     */
    protected function clearFailedAttempts(string $ipAddress): void
    {
        Cache::forget("login_attempts_{$ipAddress}");
        Cache::forget("login_blocked_{$ipAddress}");
    }
}



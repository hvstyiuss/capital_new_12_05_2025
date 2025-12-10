<?php

namespace App\Actions\User;

use App\Models\User;
use App\Services\UserService;

class DeleteUserAction
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function execute(User $user): void
    {
        $this->userService->delete($user);
    }
}






<?php

namespace App\Actions\User;

use App\Models\User;
use App\DTOs\User\UpdateUserDTO;
use App\Services\UserService;

class UpdateUserAction
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function execute(User $user, UpdateUserDTO $dto): User
    {
        $data = array_filter([
            'name' => $dto->name,
            'email' => $dto->email,
            'image' => $dto->image,
            'is_active' => $dto->isActive,
            'password' => $dto->password,
        ], fn($value) => $value !== null);

        return $this->userService->update($user, $data);
    }
}




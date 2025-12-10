<?php

namespace App\Actions\User;

use App\Models\User;
use App\DTOs\User\CreateUserDTO;
use App\Services\UserService;

class CreateUserAction
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function execute(CreateUserDTO $dto): User
    {
        return $this->userService->create([
            'ppr' => $dto->ppr,
            'name' => $dto->name,
            'password' => $dto->password,
            'email' => $dto->email,
            'image' => $dto->image,
            'is_active' => $dto->isActive ?? true,
        ]);
    }
}



<?php

namespace App\Actions\User;

use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUsersAction
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function execute(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->userService->getAll($filters, $perPage);
    }
}






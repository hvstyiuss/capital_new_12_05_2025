<?php

namespace App\Actions\User;

use App\Models\User;

class ShowUserAction
{
    public function execute(User $user): User
    {
        return $user->load(['userInfo', 'entites', 'roles']);
    }
}






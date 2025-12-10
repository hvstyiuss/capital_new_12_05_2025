<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole('admin') || $user->ppr === $model->ppr;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole('admin') || $user->ppr === $model->ppr;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}




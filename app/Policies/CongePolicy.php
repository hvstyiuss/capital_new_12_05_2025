<?php

namespace App\Policies;

use App\Models\Conge;
use App\Models\User;

class CongePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conge $conge): bool
    {
        return $user->ppr === $conge->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Conge $conge): bool
    {
        return $user->ppr === $conge->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function delete(User $user, Conge $conge): bool
    {
        return $user->hasRole('admin') || 
               ($user->ppr === $conge->ppr && !$conge->is_validated);
    }
}





<?php

namespace App\Policies;

use App\Models\Avis;
use App\Models\User;

class AvisPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Avis $avis): bool
    {
        return $user->ppr === $avis->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Avis $avis): bool
    {
        return $user->ppr === $avis->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function delete(User $user, Avis $avis): bool
    {
        return $user->hasRole('admin');
    }
}





<?php

namespace App\Policies;

use App\Models\Demande;
use App\Models\User;

class DemandePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Demande $demande): bool
    {
        return $user->ppr === $demande->ppr || $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Demande $demande): bool
    {
        return $user->ppr === $demande->ppr || $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function delete(User $user, Demande $demande): bool
    {
        return $user->hasRole('admin');
    }
}




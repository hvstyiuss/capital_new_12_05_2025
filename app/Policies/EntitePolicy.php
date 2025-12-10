<?php

namespace App\Policies;

use App\Models\Entite;
use App\Models\User;

class EntitePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Entite $entite): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Entite $entite): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if user is chef of this entity via chef_ppr
        return $entite->chef_ppr === $user->ppr;
    }

    public function delete(User $user, Entite $entite): bool
    {
        return $user->hasRole('admin');
    }
}




<?php

namespace App\Policies;

use App\Models\AvisRetour;
use App\Models\User;

class AvisRetourPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AvisRetour $avisRetour): bool
    {
        return $user->ppr === $avisRetour->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AvisRetour $avisRetour): bool
    {
        return $user->ppr === $avisRetour->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function delete(User $user, AvisRetour $avisRetour): bool
    {
        return $user->hasRole('admin');
    }
}





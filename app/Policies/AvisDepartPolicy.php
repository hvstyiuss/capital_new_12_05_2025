<?php

namespace App\Policies;

use App\Models\AvisDepart;
use App\Models\User;

class AvisDepartPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AvisDepart $avisDepart): bool
    {
        return $user->ppr === $avisDepart->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AvisDepart $avisDepart): bool
    {
        return $user->ppr === $avisDepart->ppr || 
               $user->hasRole('admin') || 
               $user->hasRole('Collaborateur Rh') ||
               $user->hasRole('super Collaborateur Rh');
    }

    public function delete(User $user, AvisDepart $avisDepart): bool
    {
        return $user->hasRole('admin');
    }
}





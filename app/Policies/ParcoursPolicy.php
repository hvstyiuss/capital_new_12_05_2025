<?php

namespace App\Policies;

use App\Models\Parcours;
use App\Models\User;

class ParcoursPolicy
{
    /**
     * Determine if the user can view any parcours.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can view the parcours.
     */
    public function view(User $user, Parcours $parcours): bool
    {
        // Users can view their own parcours, admins can view all
        return $parcours->ppr === $user->ppr || $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can create parcours.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can update the parcours.
     */
    public function update(User $user, Parcours $parcours): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can delete the parcours.
     */
    public function delete(User $user, Parcours $parcours): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }
}





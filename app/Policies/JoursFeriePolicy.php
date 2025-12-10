<?php

namespace App\Policies;

use App\Models\JoursFerie;
use App\Models\User;

class JoursFeriePolicy
{
    /**
     * Determine if the user can view any jours feries.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view holidays
    }

    /**
     * Determine if the user can view the jours ferie.
     */
    public function view(User $user, JoursFerie $joursFerie): bool
    {
        return true; // All authenticated users can view holidays
    }

    /**
     * Determine if the user can create jours feries.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can update the jours ferie.
     */
    public function update(User $user, JoursFerie $joursFerie): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can delete the jours ferie.
     */
    public function delete(User $user, JoursFerie $joursFerie): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }
}





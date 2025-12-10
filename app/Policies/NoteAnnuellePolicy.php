<?php

namespace App\Policies;

use App\Models\NoteAnnuelle;
use App\Models\User;

class NoteAnnuellePolicy
{
    /**
     * Determine if the user can view any notes annuelles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can view the note annuelle.
     */
    public function view(User $user, NoteAnnuelle $noteAnnuelle): bool
    {
        // Users can view their own notes, admins can view all
        return $noteAnnuelle->ppr === $user->ppr || $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can create notes annuelles.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can update the note annuelle.
     */
    public function update(User $user, NoteAnnuelle $noteAnnuelle): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can delete the note annuelle.
     */
    public function delete(User $user, NoteAnnuelle $noteAnnuelle): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }
}





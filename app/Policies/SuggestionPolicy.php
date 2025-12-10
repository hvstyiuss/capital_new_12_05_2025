<?php

namespace App\Policies;

use App\Models\Suggestion;
use App\Models\User;

class SuggestionPolicy
{
    /**
     * Determine if the user can view any suggestions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can view the suggestion.
     */
    public function view(User $user, Suggestion $suggestion): bool
    {
        // Users can view their own suggestions, admins can view all
        return $suggestion->ppr === $user->ppr || $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can create suggestions.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create suggestions
    }

    /**
     * Determine if the user can update the suggestion.
     */
    public function update(User $user, Suggestion $suggestion): bool
    {
        // Users can update their own pending suggestions, admins can update all
        return ($suggestion->ppr === $user->ppr && $suggestion->statut === 'pending') 
            || $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can delete the suggestion.
     */
    public function delete(User $user, Suggestion $suggestion): bool
    {
        // Users can delete their own pending suggestions, admins can delete all
        return ($suggestion->ppr === $user->ppr && $suggestion->statut === 'pending') 
            || $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }

    /**
     * Determine if the user can respond to the suggestion.
     */
    public function respond(User $user, Suggestion $suggestion): bool
    {
        return $user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']);
    }
}





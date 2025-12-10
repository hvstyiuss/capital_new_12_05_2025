<?php

namespace App\Actions\Conge;

use App\Models\Conge;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Auth\Authenticatable;

class ListCongesAction
{
    /**
     * List conges with filters and role-based scoping.
     */
    public function execute(array $filters, int $perPage, Authenticatable $user): LengthAwarePaginator
    {
        $query = Conge::with(['user']);

        // Scope to own conges unless admin/HR
        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('Collaborateur Rh') &&
            !$user->hasRole('super Collaborateur Rh')
        ) {
            $query->where('ppr', $user->ppr);
        }

        // Filter by type
        if (isset($filters['type']) && $filters['type'] !== null && $filters['type'] !== '') {
            $query->where('type', $filters['type']);
        }

        // Filter by validation status
        if (array_key_exists('is_validated', $filters) && $filters['is_validated'] !== '') {
            $query->where('is_validated', $filters['is_validated']);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}






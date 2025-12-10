<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListMutationsAction
{
    /**
     * List mutations with filters and role-based scoping.
     */
    public function execute(array $filters, int $perPage, Authenticatable $user): LengthAwarePaginator
    {
        $query = Mutation::with([
            'user',
            'toEntite',
            'approvedByCurrentDirection',
            'approvedByDestinationDirection',
            'approvedBySuperCollaborateurRh',
        ]);

        // Scope to own mutations unless admin/HR
        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('Collaborateur Rh') &&
            !$user->hasRole('super Collaborateur Rh')
        ) {
            $query->where('ppr', $user->ppr);
        }

        // Filter by mutation type
        if (!empty($filters['mutation_type'])) {
            $query->where('mutation_type', $filters['mutation_type']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $status = $filters['status'];
            if ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where('mutation_type', 'interne')
                        ->where(function ($q2) {
                            $q2->where('approved_by_current_direction', false)
                                ->orWhereNull('approved_by_current_direction');
                        })
                        ->where(function ($q2) {
                            $q2->where('rejected_by_current_direction', false)
                                ->orWhereNull('rejected_by_current_direction');
                        });
                })->orWhere(function ($q) {
                    $q->where('mutation_type', 'externe')
                        ->where(function ($q2) {
                            $q2->where('approved_by_current_direction', false)
                                ->orWhere('approved_by_destination_direction', false);
                        })
                        ->where(function ($q2) {
                            $q2->where('rejected_by_current_direction', false)
                                ->orWhereNull('rejected_by_current_direction');
                        })
                        ->where(function ($q2) {
                            $q2->where('rejected_by_destination_direction', false)
                                ->orWhereNull('rejected_by_destination_direction');
                        });
                });
            } elseif ($status === 'approved') {
                $query->where(function ($q) {
                    $q->where('mutation_type', 'interne')
                        ->where('approved_by_current_direction', true)
                        ->where('approved_by_super_collaborateur_rh', true);
                })->orWhere(function ($q) {
                    $q->where('mutation_type', 'externe')
                        ->where('approved_by_current_direction', true)
                        ->where('approved_by_destination_direction', true)
                        ->where('approved_by_super_collaborateur_rh', true);
                });
            } elseif ($status === 'rejected') {
                $query->where(function ($q) {
                    $q->where('rejected_by_current_direction', true)
                        ->orWhere('rejected_by_destination_direction', true)
                        ->orWhere('rejected_by_super_rh', true);
                });
            }
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('fname', 'like', '%' . $search . '%')
                        ->orWhere('lname', 'like', '%' . $search . '%')
                        ->orWhere('ppr', 'like', '%' . $search . '%');
                })->orWhereHas('toEntite', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}






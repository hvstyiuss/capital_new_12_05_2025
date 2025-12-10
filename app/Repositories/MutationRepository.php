<?php

namespace App\Repositories;

use App\Models\Mutation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MutationRepository extends BaseRepository
{
    public function __construct(Mutation $model)
    {
        parent::__construct($model);
    }

    /**
     * Get mutations by PPR.
     */
    public function getByPpr(string $ppr, array $relations = []): Collection
    {
        $query = $this->model->where('ppr', $ppr);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->get();
    }

    /**
     * Get mutations by PPR with year filter.
     */
    public function getByPprAndYear(string $ppr, ?int $year = null, array $relations = []): Collection
    {
        $query = $this->model->where('ppr', $ppr);
        
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->get();
    }

    /**
     * Get mutations by PPR for last N years.
     */
    public function getByPprForLastYears(string $ppr, int $years, array $relations = []): Collection
    {
        $query = $this->model->where('ppr', $ppr)
            ->whereYear('created_at', '>=', date('Y') - $years);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get paginated mutations by PPR.
     */
    public function getPaginatedByPpr(string $ppr, int $perPage = 25, array $filters = [], array $relations = []): LengthAwarePaginator
    {
        $query = $this->model->where('ppr', $ppr);
        
        if (isset($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('toEntite', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Check if user has pending mutations.
     */
    public function hasPendingMutations(string $ppr): bool
    {
        return $this->model->where('ppr', $ppr)
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('mutation_type', 'interne')
                       ->where(function($q3) {
                           $q3->where('approved_by_current_direction', false)
                              ->orWhereNull('approved_by_current_direction');
                       })
                       ->where(function($q3) {
                           $q3->where('rejected_by_current_direction', false)
                              ->orWhereNull('rejected_by_current_direction');
                       });
                })
                ->orWhere(function($q2) {
                    $q2->where('mutation_type', 'externe')
                       ->where(function($q3) {
                           $q3->where('approved_by_current_direction', false)
                              ->orWhere('approved_by_destination_direction', false);
                       })
                       ->where(function($q3) {
                           $q3->where('rejected_by_current_direction', false)
                              ->orWhereNull('rejected_by_current_direction');
                       })
                       ->where(function($q3) {
                           $q3->where('rejected_by_destination_direction', false)
                              ->orWhereNull('rejected_by_destination_direction');
                       });
                })
                ->where(function($q2) {
                    $q2->where('rejected_by_super_rh', false)
                       ->orWhereNull('rejected_by_super_rh');
                });
            })
            ->exists();
    }

    /**
     * Count mutations by PPR and status.
     */
    public function countByPprAndStatus(string $ppr, string $status): int
    {
        $query = $this->model->where('ppr', $ppr);
        
        if ($status === 'pending') {
            $query->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('mutation_type', 'interne')
                       ->where(function($q3) {
                           $q3->where('approved_by_current_direction', false)
                              ->orWhereNull('approved_by_current_direction');
                       })
                       ->where(function($q3) {
                           $q3->where('rejected_by_current_direction', false)
                              ->orWhereNull('rejected_by_current_direction');
                       });
                })
                ->orWhere(function($q2) {
                    $q2->where('mutation_type', 'externe')
                       ->where(function($q3) {
                           $q3->where('approved_by_current_direction', false)
                              ->orWhere('approved_by_destination_direction', false);
                       })
                       ->where(function($q3) {
                           $q3->where('rejected_by_current_direction', false)
                              ->orWhereNull('rejected_by_current_direction');
                       })
                       ->where(function($q3) {
                           $q3->where('rejected_by_destination_direction', false)
                              ->orWhereNull('rejected_by_destination_direction');
                       });
                })
                ->where(function($q2) {
                    $q2->where('rejected_by_super_rh', false)
                       ->orWhereNull('rejected_by_super_rh');
                });
            });
        } elseif ($status === 'approved') {
            $query->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('mutation_type', 'interne')
                       ->where('approved_by_current_direction', true)
                       ->where('approved_by_super_collaborateur_rh', true);
                })
                ->orWhere(function($q2) {
                    $q2->where('mutation_type', 'externe')
                       ->where('approved_by_current_direction', true)
                       ->where('approved_by_destination_direction', true)
                       ->where('approved_by_super_collaborateur_rh', true);
                });
            });
        } elseif ($status === 'rejected') {
            $query->where(function($q) {
                $q->where('rejected_by_current_direction', true)
                  ->orWhere('rejected_by_destination_direction', true)
                  ->orWhere('rejected_by_super_rh', true);
            });
        }
        
        return $query->count();
    }
}



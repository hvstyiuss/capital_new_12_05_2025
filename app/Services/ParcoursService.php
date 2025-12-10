<?php

namespace App\Services;

use App\Models\Parcours;
use App\Repositories\ParcoursRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ParcoursService
{
    protected ParcoursRepository $parcoursRepository;

    public function __construct(ParcoursRepository $parcoursRepository)
    {
        $this->parcoursRepository = $parcoursRepository;
    }

    /**
     * Get all parcours with optional filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Parcours::with(['user.userInfo', 'entite', 'grade']);

        // Search functionality
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $parcoursIds = collect();
            
            $parcoursIds = $parcoursIds->merge(
                Parcours::where('poste', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->pluck('id')
            );
            
            $parcoursIds = $parcoursIds->merge(
                Parcours::whereHas('user', function($q) use ($search) {
                    $q->where('fname', 'like', "%{$search}%")
                       ->orWhere('lname', 'like', "%{$search}%")
                       ->orWhere('ppr', 'like', "%{$search}%");
                })->pluck('id')
            );
            
            $parcoursIds = $parcoursIds->merge(
                Parcours::whereHas('entite', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->pluck('id')
            );
            
            $query->whereIn('id', $parcoursIds->unique());
        }

        // Filter by entity
        if (isset($filters['entite']) && $filters['entite']) {
            $query->where('entite_id', $filters['entite']);
        }

        // Filter by status
        if (isset($filters['status']) && $filters['status']) {
            if ($filters['status'] === 'active') {
                $query->where(function($q) {
                    $q->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
                });
            } elseif ($filters['status'] === 'inactive') {
                $query->whereNotNull('date_fin')
                      ->where('date_fin', '<', now());
            }
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('date_debut', 'desc')->paginate($perPage);
    }

    /**
     * Get parcours by PPR.
     */
    public function getByPpr(string $ppr): Collection
    {
        return Parcours::where('ppr', $ppr)
            ->with(['entite.parent', 'grade'])
            ->orderBy('date_debut', 'desc')
            ->get();
    }

    /**
     * Create a new parcours.
     */
    public function create(array $data): Parcours
    {
        return $this->parcoursRepository->create($data);
    }

    /**
     * Update a parcours.
     */
    public function update(Parcours $parcours, array $data): Parcours
    {
        return $this->parcoursRepository->update($parcours->id, $data);
    }

    /**
     * Delete a parcours.
     */
    public function delete(Parcours $parcours): bool
    {
        return $this->parcoursRepository->delete($parcours->id);
    }
}





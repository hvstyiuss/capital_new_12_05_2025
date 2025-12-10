<?php

namespace App\Services;

use App\Models\Entite;
use App\Repositories\EntiteRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EntiteService
{
    protected EntiteRepository $entiteRepository;

    public function __construct(EntiteRepository $entiteRepository)
    {
        $this->entiteRepository = $entiteRepository;
    }

    /**
     * Get paginated entities for API (with filters).
     */
    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->entiteRepository->with(['parent', 'children', 'entiteInfo']);

        // Search by name or description
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('entiteInfo', function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by type (central / regional, etc.)
        if (!empty($filters['type'])) {
            $query->whereHas('entiteInfo', function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            });
        }

        // Filter by entity_type
        if (array_key_exists('entity_type', $filters) && $filters['entity_type'] !== null && $filters['entity_type'] !== '') {
            $query->where('entity_type', $filters['entity_type']);
        }

        return $query
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get lightweight list of all entities (id + name) for selects.
     */
    public function getAllForSelect(): Collection
    {
        return $this->entiteRepository
            ->getModel()
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
    }

    /**
     * Get full dataset for frontend filtering (entities index page).
     */
    public function getAllForFrontend(): Collection
    {
        return $this->entiteRepository
            ->with(['parent', 'children', 'entiteInfo'])
            ->orderBy('name', 'asc')
            ->get()
            ->map(function (Entite $entite) {
                return [
                    'id' => $entite->id,
                    'name' => $entite->name,
                    'type' => $entite->entiteInfo ? $entite->entiteInfo->type : '',
                    'description' => $entite->entiteInfo ? $entite->entiteInfo->description : '',
                    'parent_id' => $entite->parent_id,
                    'parent_name' => $entite->parent ? $entite->parent->name : null,
                    'date_debut' => $entite->date_debut ? $entite->date_debut->toISOString() : null,
                    'date_fin' => $entite->date_fin ? $entite->date_fin->toISOString() : null,
                    'children_count' => $entite->children ? $entite->children->count() : 0,
                    'chef_ppr' => $entite->chef_ppr,
                ];
            });
    }

    /**
     * Create a new entite.
     */
    public function create(array $data): Entite
    {
        return $this->entiteRepository->create($data);
    }

    /**
     * Update an entite.
     */
    public function update(Entite $entite, array $data): Entite
    {
        return $this->entiteRepository->update($entite->getKey(), $data);
    }

    /**
     * Delete an entite.
     */
    public function delete(Entite $entite): bool
    {
        return $this->entiteRepository->delete($entite->getKey());
    }

    /**
     * Get all descendant IDs of an entity (to prevent circular references)
     */
    public function getDescendants(Entite $entite, $descendants = []): array
    {
        foreach ($entite->children as $child) {
            $descendants[] = $child->id;
            $descendants = $this->getDescendants($child, $descendants);
        }
        return $descendants;
    }
}


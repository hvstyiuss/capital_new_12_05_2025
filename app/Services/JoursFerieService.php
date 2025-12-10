<?php

namespace App\Services;

use App\Models\JoursFerie;
use App\Repositories\JoursFerieRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class JoursFerieService
{
    protected JoursFerieRepository $joursFerieRepository;

    public function __construct(JoursFerieRepository $joursFerieRepository)
    {
        $this->joursFerieRepository = $joursFerieRepository;
    }

    /**
     * Get all jours feries with optional filters.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = JoursFerie::with('typeJoursFerie');

        // Filter by year
        if (isset($filters['year']) && $filters['year']) {
            $query->whereYear('date', $filters['year']);
        }

        // Filter by type
        if (isset($filters['type']) && $filters['type']) {
            $query->whereHas('typeJoursFerie', function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['type'] . '%');
            });
        }

        // Search by name
        if (isset($filters['search']) && $filters['search']) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('date')->get();
    }

    /**
     * Get available years.
     */
    public function getAvailableYears(): SupportCollection
    {
        return JoursFerie::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }

    /**
     * Create a new jours ferie.
     */
    public function create(array $data): JoursFerie
    {
        return $this->joursFerieRepository->create($data);
    }

    /**
     * Update a jours ferie.
     */
    public function update(JoursFerie $joursFerie, array $data): JoursFerie
    {
        return $this->joursFerieRepository->update($joursFerie->id, $data);
    }

    /**
     * Delete a jours ferie.
     */
    public function delete(JoursFerie $joursFerie): bool
    {
        return $this->joursFerieRepository->delete($joursFerie->id);
    }
}




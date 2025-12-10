<?php

namespace App\Services;

use App\Models\NoteAnnuelle;
use App\Repositories\NoteAnnuelleRepository;
use Illuminate\Database\Eloquent\Collection;

class NoteAnnuelleService
{
    protected NoteAnnuelleRepository $noteAnnuelleRepository;

    public function __construct(NoteAnnuelleRepository $noteAnnuelleRepository)
    {
        $this->noteAnnuelleRepository = $noteAnnuelleRepository;
    }

    /**
     * Get all notes annuelles for a user.
     */
    public function getByPpr(string $ppr): Collection
    {
        return NoteAnnuelle::where('ppr', $ppr)
            ->orderBy('annee', 'asc')
            ->get();
    }

    /**
     * Get all notes annuelles with optional filters.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = NoteAnnuelle::with(['user']);

        // Filter by PPR
        if (isset($filters['ppr']) && $filters['ppr']) {
            $query->where('ppr', $filters['ppr']);
        }

        // Filter by year
        if (isset($filters['annee']) && $filters['annee']) {
            $query->where('annee', $filters['annee']);
        }

        return $query->orderBy('annee', 'desc')->get();
    }

    /**
     * Create a new note annuelle.
     */
    public function create(array $data): NoteAnnuelle
    {
        return $this->noteAnnuelleRepository->create($data);
    }

    /**
     * Update a note annuelle.
     */
    public function update(NoteAnnuelle $noteAnnuelle, array $data): NoteAnnuelle
    {
        return $this->noteAnnuelleRepository->update($noteAnnuelle->id, $data);
    }

    /**
     * Delete a note annuelle.
     */
    public function delete(NoteAnnuelle $noteAnnuelle): bool
    {
        return $this->noteAnnuelleRepository->delete($noteAnnuelle->id);
    }
}





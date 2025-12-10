<?php

namespace App\Services;

use App\Models\Suggestion;
use App\Repositories\SuggestionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SuggestionService
{
    protected SuggestionRepository $suggestionRepository;

    public function __construct(SuggestionRepository $suggestionRepository)
    {
        $this->suggestionRepository = $suggestionRepository;
    }

    /**
     * Get all suggestions with optional filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Suggestion::with(['user']);

        // Filter by status
        if (isset($filters['statut']) && $filters['statut']) {
            $query->where('statut', $filters['statut']);
        }

        // Filter by user's own suggestions
        if (isset($filters['ppr']) && $filters['ppr']) {
            $query->where('ppr', $filters['ppr']);
        }

        // Search
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('sujet', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new suggestion.
     */
    public function create(array $data): Suggestion
    {
        return $this->suggestionRepository->create($data);
    }

    /**
     * Update a suggestion.
     */
    public function update(Suggestion $suggestion, array $data): Suggestion
    {
        return $this->suggestionRepository->update($suggestion->id, $data);
    }

    /**
     * Respond to a suggestion.
     */
    public function respond(Suggestion $suggestion, string $reponse, string $reponduPar): Suggestion
    {
        $data = [
            'reponse' => $reponse,
            'repondu_par' => $reponduPar,
            'repondu_le' => now(),
            'statut' => 'responded',
        ];
        return $this->suggestionRepository->update($suggestion->id, $data);
    }

    /**
     * Delete a suggestion.
     */
    public function delete(Suggestion $suggestion): bool
    {
        return $this->suggestionRepository->delete($suggestion->id);
    }
}





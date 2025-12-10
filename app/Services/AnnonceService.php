<?php

namespace App\Services;

use App\Models\Annonce;
use App\Models\User;
use App\Repositories\AnnonceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AnnonceService
{
    protected AnnonceRepository $annonceRepository;

    public function __construct(AnnonceRepository $annonceRepository)
    {
        $this->annonceRepository = $annonceRepository;
    }

    /**
     * Get paginated annonces with filters applied.
     */
    public function getAll(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Annonce::with(['user', 'typeAnnonce', 'entites']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('content', 'like', "%{$search}%");
        }

        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (!empty($filters['type'])) {
            $query->where('type_annonce_id', $filters['type']);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($filters);
    }

    /**
     * Create a new annonce from validated data.
     *
     * @param  array       $validated  Validated data from StoreAnnonceRequest
     * @param  User        $user       Currently authenticated user
     * @param  UploadedFile|null $imageFile Optional uploaded image
     */
    public function create(array $validated, User $user, ?UploadedFile $imageFile = null): Annonce
    {
        $data = [
            'content'         => $validated['content'],
            'type_annonce_id' => $validated['type_annonce_id'],
            'statut'          => $validated['statut'] ?? 'active',
        ];

        // PPR is optional; default to authenticated user's PPR
        if (!empty($validated['ppr'])) {
            $data['ppr'] = $validated['ppr'];
        } else {
            $data['ppr'] = $user->ppr;
        }

        if ($imageFile) {
            $data['image'] = $imageFile->store('annonces', 'public');
        }

        /** @var Annonce $annonce */
        $annonce = $this->annonceRepository->create($data);

        // Attach entities (many-to-many) - required in request
        if (!empty($validated['entites']) && is_array($validated['entites'])) {
            $annonce->entites()->sync($validated['entites']);
        }

        return $annonce->load(['user', 'typeAnnonce', 'entites']);
    }

    /**
     * Update an existing annonce from validated data.
     *
     * @param  Annonce     $annonce
     * @param  array       $validated
     * @param  User        $user
     * @param  UploadedFile|null $imageFile
     */
    public function update(Annonce $annonce, array $validated, User $user, ?UploadedFile $imageFile = null): Annonce
    {
        $data = [];

        if (array_key_exists('content', $validated)) {
            $data['content'] = $validated['content'];
        }

        if (array_key_exists('type_annonce_id', $validated)) {
            $data['type_annonce_id'] = $validated['type_annonce_id'];
        }

        if (array_key_exists('statut', $validated)) {
            $data['statut'] = $validated['statut'];
        } else {
            $data['statut'] = $annonce->statut;
        }

        if (!empty($validated['ppr'])) {
            $data['ppr'] = $validated['ppr'];
        } else {
            $data['ppr'] = $user->ppr;
        }

        if ($imageFile) {
            // Delete old image if exists
            if ($annonce->image) {
                Storage::disk('public')->delete($annonce->image);
            }
            $data['image'] = $imageFile->store('annonces', 'public');
        }

        $this->annonceRepository->update($annonce->id, $data);

        // Sync entities (many-to-many) if provided
        if (array_key_exists('entites', $validated) && is_array($validated['entites'])) {
            $annonce->entites()->sync($validated['entites']);
        }

        return $annonce->fresh(['user', 'typeAnnonce', 'entites']);
    }

    /**
     * Delete an annonce and its image.
     */
    public function delete(Annonce $annonce): void
    {
        if ($annonce->image) {
            Storage::disk('public')->delete($annonce->image);
        }

        $this->annonceRepository->delete($annonce->id);
    }
}






<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get paginated users with filters.
     */
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->userRepository
            ->with(['userInfo', 'entites', 'roles']);

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                    ->orWhere('lname', 'like', "%{$search}%")
                    ->orWhere('ppr', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // is_active filter
        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        return $query
            ->orderBy('fname')
            ->orderBy('lname')
            ->paginate($perPage);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return $this->userRepository->create($data);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        return $this->userRepository->update($user->getKey(), $data);
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $this->userRepository->delete($user->getKey());
    }
}



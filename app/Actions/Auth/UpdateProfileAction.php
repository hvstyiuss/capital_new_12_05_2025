<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\DTOs\Auth\UpdateProfileDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\ProfileImageService;

class UpdateProfileAction
{
    protected ProfileImageService $profileImageService;

    public function __construct(ProfileImageService $profileImageService)
    {
        $this->profileImageService = $profileImageService;
    }

    /**
     * Update the authenticated user's profile data.
     */
    public function execute(User $user, UpdateProfileDTO $dto): void
    {
        // Update name
        $user->update([
            'name' => $dto->name,
        ]);

        // Handle password change if requested
        if ($dto->newPassword !== null) {
            $user->update([
                'password' => Hash::make($dto->newPassword),
            ]);
        }

        // Handle image upload using ProfileImageService
        if ($dto->image !== null) {
            $this->profileImageService->uploadProfileImage($user, $dto->image);
        }
    }
}



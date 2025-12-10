<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\DTOs\Auth\UpdateProfileDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateProfileAction
{
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

        // Handle image upload
        if ($dto->image !== null) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $dto->image->store('users', 'public');

            $user->update([
                'image' => $imagePath,
            ]);
        }
    }
}



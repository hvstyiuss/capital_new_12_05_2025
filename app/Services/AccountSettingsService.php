<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AccountSettingsService
{
    /**
     * Update user personal information.
     */
    public function updatePersonalInfo(User $user, array $validated, ?UploadedFile $image = null): void
    {
        // Prepare update data
        $updateData = [];
        
        // Split name into fname and lname
        if (isset($validated['name'])) {
            $nameParts = explode(' ', trim($validated['name']), 2);
            $updateData['fname'] = $nameParts[0];
            $updateData['lname'] = $nameParts[1] ?? null;
        }

        // Only update email if it's provided and not empty
        if (!empty($validated['email'])) {
            $updateData['email'] = $validated['email'];
        }

        // Update user
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // Update or create userInfo
        if (!$user->userInfo) {
            $user->userInfo()->create(['ppr' => $user->ppr]);
            $user->load('userInfo');
        }

        $userInfoUpdate = [];
        
        // Only update email in userInfo if email was provided in the request
        if (!empty($validated['email'])) {
            $userInfoUpdate['email'] = $validated['email'];
        }

        // Handle image upload - store in user_infos.photo
        if ($image) {
            $this->updateProfileImage($user, $image);
        }
        
        if (!empty($userInfoUpdate)) {
            $user->userInfo->update($userInfoUpdate);
            $user->load('userInfo');
        }
    }

    /**
     * Update user password.
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(User $user, array $validated): void
    {
        // Ensure userSettings exists
        if (!$user->userSetting) {
            $user->userSetting()->create([
                'ppr' => $user->ppr,
                'language' => 'fr',
                'theme' => 'light',
                'timezone' => 'Africa/Casablanca',
                'notifications_email' => true,
                'notifications_sms' => false,
                'dark_mode' => false,
                'two_factor_enabled' => false,
            ]);
            $user->load('userSetting');
        }

        // Update notification preferences in user_settings table
        $user->userSetting->update([
            'notifications_email' => isset($validated['email_notifications']) ? (bool) $validated['email_notifications'] : false,
            'notifications_sms' => isset($validated['push_notifications']) ? (bool) $validated['push_notifications'] : false,
        ]);
    }

    /**
     * Update profile image.
     */
    public function updateProfileImage(User $user, UploadedFile $image): string
    {
        // Ensure userInfo exists
        if (!$user->userInfo) {
            $user->userInfo()->create(['ppr' => $user->ppr]);
            $user->load('userInfo');
        }

        // Delete old image if exists
        if ($user->userInfo->photo && Storage::disk('public')->exists($user->userInfo->photo)) {
            Storage::disk('public')->delete($user->userInfo->photo);
        }

        // Store new image
        $imagePath = $image->store('users', 'public');
        $user->userInfo->update(['photo' => $imagePath]);
        $user->load('userInfo');

        return $imagePath;
    }

    /**
     * Delete profile image.
     */
    public function deleteProfileImage(User $user): bool
    {
        if (!$user->userInfo || !$user->userInfo->photo) {
            return false;
        }

        // Delete image file
        if (Storage::disk('public')->exists($user->userInfo->photo)) {
            Storage::disk('public')->delete($user->userInfo->photo);
        }

        // Update userInfo to remove photo reference
        $user->userInfo->update(['photo' => null]);
        $user->load('userInfo');

        return true;
    }
}


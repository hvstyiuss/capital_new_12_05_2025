<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileImageService
{
    /**
     * Maximum file size in bytes (7 MB)
     */
    private const MAX_FILE_SIZE = 7 * 1024 * 1024;

    /**
     * Allowed mime types
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    /**
     * Allowed extensions
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    /**
     * Storage disk
     */
    private const STORAGE_DISK = 'public';

    /**
     * Storage path
     */
    private const STORAGE_PATH = 'profile-images';

    /**
     * Upload and store profile image
     *
     * @param User $user
     * @param UploadedFile $image
     * @return string Path to stored image
     * @throws \Exception
     */
    public function uploadProfileImage(User $user, UploadedFile $image): string
    {
        $this->validateImage($image);

        // Ensure userInfo exists
        if (!$user->userInfo) {
            $user->userInfo()->create(['ppr' => $user->ppr]);
            $user->load('userInfo');
        }

        // Delete old image if exists
        $this->deleteProfileImage($user);

        // Generate filename: ppr.extension
        $extension = strtolower($image->getClientOriginalExtension());
        $filename = $user->ppr . '.' . $extension;
        $path = self::STORAGE_PATH . '/' . $filename;

        // Store image
        $storedPath = $image->storeAs(self::STORAGE_PATH, $filename, self::STORAGE_DISK);

        // Update userInfo
        $user->userInfo->update(['photo' => $storedPath]);
        $user->load('userInfo');

        return $storedPath;
    }

    /**
     * Delete profile image
     *
     * @param User $user
     * @return bool
     */
    public function deleteProfileImage(User $user): bool
    {
        if (!$user->userInfo || !$user->userInfo->photo) {
            return false;
        }

        $photoPath = $user->userInfo->photo;

        // Delete from storage
        if (Storage::disk(self::STORAGE_DISK)->exists($photoPath)) {
            Storage::disk(self::STORAGE_DISK)->delete($photoPath);
        }

        // Update database
        $user->userInfo->update(['photo' => null]);
        $user->load('userInfo');

        return true;
    }

    /**
     * Get profile image URL
     *
     * @param User $user
     * @return string|null
     */
    public function getProfileImageUrl(User $user): ?string
    {
        if ($user->userInfo && $user->userInfo->photo) {
            // Use asset() for local development compatibility
            return asset('storage/' . $user->userInfo->photo);
        }

        if ($user->image) {
            // Use asset() for local development compatibility
            return asset('storage/' . $user->image);
        }

        return null;
    }

    /**
     * Validate uploaded image
     *
     * @param UploadedFile $image
     * @return void
     * @throws \Exception
     */
    private function validateImage(UploadedFile $image): void
    {
        // Check file size
        if ($image->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('L\'image ne peut pas dépasser 7 Mo.');
        }

        // Check mime type
        $mimeType = $image->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Le format de l\'image doit être JPG, JPEG ou PNG.');
        }

        // Check extension
        $extension = strtolower($image->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception('L\'extension de l\'image doit être .jpg, .jpeg ou .png.');
        }
    }
}


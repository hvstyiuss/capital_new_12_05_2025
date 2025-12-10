<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdatePersonalInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateNotificationsRequest;
use App\Http\Requests\UpdateProfileImageRequest;
use App\Services\AccountSettingsService;

class AccountSettingsController extends Controller
{
    protected AccountSettingsService $accountSettingsService;

    public function __construct(AccountSettingsService $accountSettingsService)
    {
        $this->accountSettingsService = $accountSettingsService;
    }

    /**
     * Display account settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load(['userInfo', 'userSetting']);
        return view('account-settings.index', compact('user'));
    }

    /**
     * Update personal information.
     */
    public function updatePersonalInfo(UpdatePersonalInfoRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $image = $request->hasFile('image') ? $request->file('image') : null;

        $this->accountSettingsService->updatePersonalInfo($user, $validated, $image);

        return redirect()->route('account-settings.index')
            ->with('success', 'Vos informations personnelles ont été mises à jour avec succès.');
    }

    /**
     * Update password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $this->accountSettingsService->updatePassword($user, $validated['new_password']);

        return redirect()->route('account-settings.index')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(UpdateNotificationsRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $this->accountSettingsService->updateNotifications($user, $validated);

        return redirect()->route('account-settings.index')
            ->with('success', 'Vos préférences de notification ont été mises à jour avec succès.');
    }

    /**
     * Update profile image only.
     */
    public function updateProfileImage(UpdateProfileImageRequest $request)
    {
        $user = Auth::user();
        $imagePath = $this->accountSettingsService->updateProfileImage($user, $request->file('image'));

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès.',
                'image_url' => asset('storage/' . $imagePath),
            ]);
        }

        return redirect()->route('account-settings.index')
            ->with('success', 'Photo de profil mise à jour avec succès.');
    }

    /**
     * Delete profile image.
     */
    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();
        $deleted = $this->accountSettingsService->deleteProfileImage($user);

        if (!$deleted) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune photo de profil à supprimer.',
                ], 404);
            }
            return redirect()->route('account-settings.index')
                ->with('error', 'Aucune photo de profil à supprimer.');
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès.',
            ]);
        }

        return redirect()->route('account-settings.index')
            ->with('success', 'Photo de profil supprimée avec succès.');
    }
}


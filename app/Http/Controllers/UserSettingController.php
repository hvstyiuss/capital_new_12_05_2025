<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    /**
     * Display the user's settings.
     */
    public function index()
    {
        $user = Auth::user();
        $settings = UserSetting::firstOrCreate(
            ['ppr' => $user->ppr],
            [
                'language' => 'fr',
                'theme' => 'light',
                'timezone' => 'Africa/Casablanca',
                'notifications_email' => true,
                'notifications_sms' => false,
                'dark_mode' => false,
                'two_factor_enabled' => false,
            ]
        );

        return view('user-settings.index', compact('settings'));
    }

    /**
     * Show the form for editing the user's settings.
     */
    public function edit()
    {
        $user = Auth::user();
        $settings = UserSetting::firstOrCreate(
            ['ppr' => $user->ppr],
            [
                'language' => 'fr',
                'theme' => 'light',
                'timezone' => 'Africa/Casablanca',
                'notifications_email' => true,
                'notifications_sms' => false,
                'dark_mode' => false,
                'two_factor_enabled' => false,
            ]
        );

        return view('user-settings.edit', compact('settings'));
    }

    /**
     * Update the user's settings.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'language' => 'required|string|in:fr,ar,en',
            'theme' => 'required|string|in:light,dark,auto',
            'timezone' => 'required|string|max:50',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
            'dark_mode' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ]);

        $settings = UserSetting::updateOrCreate(
            ['ppr' => $user->ppr],
            $validated
        );

        return redirect()->route('user-settings.index')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }

    /**
     * Update settings via AJAX.
     */
    public function updateAjax(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'language' => 'sometimes|string|in:fr,ar,en',
            'theme' => 'sometimes|string|in:light,dark,auto',
            'timezone' => 'sometimes|string|max:50',
            'notifications_email' => 'sometimes|boolean',
            'notifications_sms' => 'sometimes|boolean',
            'dark_mode' => 'sometimes|boolean',
            'two_factor_enabled' => 'sometimes|boolean',
        ]);

        $settings = UserSetting::updateOrCreate(
            ['ppr' => $user->ppr],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Paramètres mis à jour avec succès.',
            'settings' => $settings
        ]);
    }
}

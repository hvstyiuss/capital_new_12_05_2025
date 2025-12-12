<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Actions\Auth\ShowLoginAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\RefreshCaptchaAction;
use App\Actions\Auth\UpdateProfileAction;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $data = app(ShowLoginAction::class)->execute(request()->ip());

        // Filter errors for display (exclude blocked login error and PPR credential error)
        $filteredErrors = [];
        if ($request->session()->has('errors')) {
            $errors = $request->session()->get('errors');
            $allErrors = $errors->all();
            
            // Filter out blocked login error if we're showing the block alert
            if (isset($data['isBlocked']) && $data['isBlocked']) {
                $allErrors = array_filter($allErrors, function($error) {
                    return !str_contains($error, 'Trop de tentatives de connexion échouées');
                });
            }
            
            // Filter out PPR credential error
            $allErrors = array_filter($allErrors, function($error) {
                return !str_contains($error, 'Les identifiants fournis ne correspondent pas à nos enregistrements');
            });
            
            $filteredErrors = array_values($allErrors);
        }
        
        $data['filteredErrors'] = $filteredErrors;

        return view('auth.login', $data);
    }

    public function login(LoginRequest $request)
    {
        $dto = new \App\DTOs\Auth\LoginDTO(
            ppr: $request->input('ppr'),
            password: $request->input('password'),
            captcha: (int) $request->input('captcha'),
            remember: $request->boolean('remember'),
            ipAddress: $request->ip()
        );

        $result = app(LoginAction::class)->execute($dto);

        if ($result['success']) {
            return redirect()->route('dashboard');
        }

        // If blocked, simple redirect with captcha prepared in showLogin
        if ($result['blocked']) {
            return redirect()->route('login')
                ->withInput($request->only('ppr'))
                ->with($result['captcha'] ?? []);
        }

        // Not blocked but failed: send back errors and refreshed captcha
        return redirect()->route('login')
            ->withErrors($result['errors'] ?? [])
            ->withInput($request->only('ppr'))
            ->with($result['captcha'] ?? []);
    }

    public function logout(Request $request)
    {
        app(LogoutAction::class)->execute();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function refreshCaptcha()
    {
        $captcha = app(RefreshCaptchaAction::class)->execute();

        return response()->json([
            'question' => $captcha['captcha_question'],
            'answer'   => $captcha['captcha_answer'],
        ]);
    }

    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:7168', // 7 MB in kilobytes
        ], [
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format JPG, JPEG ou PNG.',
            'image.max' => 'L\'image ne peut pas dépasser 7 Mo.',
        ]);

        // Handle password change preconditions at controller (validation-level rules)
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password')) {
                return back()->withErrors([
                    'current_password' => 'Le mot de passe actuel est requis pour changer le mot de passe.',
                ])->withInput();
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Le mot de passe actuel est incorrect.',
                ])->withInput();
            }
        }

        $dto = new \App\DTOs\Auth\UpdateProfileDTO(
            name: $validated['name'],
            currentPassword: $validated['current_password'] ?? null,
            newPassword: $validated['new_password'] ?? null,
            image: $request->file('image')
        );

        app(UpdateProfileAction::class)->execute($user, $dto);

        return redirect()->route('auth.profile')->with('success', 'Votre profil a été mis à jour avec succès.');
    }

}

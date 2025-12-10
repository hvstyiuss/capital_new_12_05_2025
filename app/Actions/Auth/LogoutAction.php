<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    /**
     * Log out the current user.
     */
    public function execute(): void
    {
        Auth::logout();
    }
}






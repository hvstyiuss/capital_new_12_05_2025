<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share user settings and chef status with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
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
                $view->with('userSettings', $settings);
                
                // Share chef status
                $isChef = $user->isChef();
                $view->with('isChef', $isChef);
            }
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;
use App\Models\Entite;
use App\Services\MutationService;

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

        // Share user settings, chef status, and notifications with all views
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
                
                // Share RH stats role
                $hasRhStatsRole = $user->hasRole('admin') || 
                                  $user->hasRole('Collaborateur Rh') || 
                                  $user->hasRole('super Collaborateur Rh');
                $view->with('hasRhStatsRole', $hasRhStatsRole);
                
                // Share director and special chef status for mutations
                $isDirector = method_exists($user, 'isDirectorOfDirection') ? $user->isDirectorOfDirection() : false;
                $mutationService = app(MutationService::class);
                $isSpecialChef = $mutationService->isChefOfSpecialEntity($user);
                $view->with('isDirector', $isDirector);
                $view->with('isSpecialChef', $isSpecialChef);
                
                // Share super RH role
                $hasSuperRhRole = $user->hasRole('super Collaborateur Rh');
                $view->with('hasSuperRhRole', $hasSuperRhRole);
                
                // Share chef entities (only if chef and not RH stats role)
                $chefEntites = collect();
                if ($isChef && !$hasRhStatsRole) {
                    $chefEntites = Entite::where('chef_ppr', $user->ppr)
                        ->with('entiteInfo')
                        ->get();
                }
                $view->with('chefEntites', $chefEntites);
                
                // Share notifications data (avoid N+1 queries)
                $unreadCount = $user->notifications()->whereNull('read_at')->count();
                $recentNotifications = $user->notifications()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                
                $view->with('unreadNotificationCount', $unreadCount);
                $view->with('recentNotifications', $recentNotifications);
                $view->with('currentUser', $user);
            } else {
                $view->with('unreadNotificationCount', 0);
                $view->with('recentNotifications', collect());
                $view->with('currentUser', null);
            }
        });
    }
}

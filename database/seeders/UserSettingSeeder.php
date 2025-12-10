<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserSetting;

class UserSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            UserSetting::updateOrCreate(
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
        }
    }
}

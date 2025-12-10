<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Parcours;
use App\Models\Entite;
use App\Models\Grade;
use App\Models\UserInfo;
use App\Models\UserSetting;
use App\Models\Echelle;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create admin user
        $admin = User::updateOrCreate(
            ['ppr' => '001'],
            [
                'fname' => 'Admin',
                'lname' => '',
                'email' => '001@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_deleted' => false,
            ]
        );
        
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create manager user
        $manager = User::updateOrCreate(
            ['ppr' => '002'],
            [
                'fname' => 'Youssef',
                'lname' => 'Ghannam',
                'email' => '002@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_deleted' => false,
            ]
        );
        
        if (!$manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }
        

        // Get or create echelle
        $echelle = Echelle::first();
        if (!$echelle) {
            $echelle = Echelle::create([
                'name' => 'Echelle 1',
            ]);
        }

        // Get first grade
        $grade = Grade::first();
        if (!$grade) {
            $grade = Grade::create([
                'name' => 'Technicien 3eme',
                'echelle_id' => $echelle->id,
            ]);
        }

        // Define users array (service administratif has been removed)
        $users = [];
        // Add users here if needed, example structure:
        // $users = [
        //     [
        //         'ppr' => '003',
        //         'fname' => 'John',
        //         'lname' => 'Doe',
        //         'email' => '003@example.com',
        //         'role' => 'user',
        //         'entite' => Entite::where('name', 'Some Entity')->first(),
        //         'is_chef' => false,
        //     ],
        // ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['ppr' => $userData['ppr']],
                [
                    'fname' => $userData['fname'],
                    'lname' => $userData['lname'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            );

            // Assign role
            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }

            // Create parcours entry
            Parcours::updateOrCreate(
                [
                    'ppr' => $user->ppr,
                    'entite_id' => $userData['entite']->id,
                ],
                [
                    'poste' => $userData['is_chef'] ? 'Chef de Service' : 'Employé',
                    'date_debut' => Carbon::now()->subYears(rand(1, 5)),
                    'date_fin' => null,
                    'grade_id' => $grade->id,
                ]
            );

            // If user is chef, set chef_ppr in entites table
            if ($userData['is_chef']) {
                $userData['entite']->update(['chef_ppr' => $user->ppr]);
            }

            // Create user_info entry
            UserInfo::updateOrCreate(
                ['ppr' => $user->ppr],
                [
                    'email' => $userData['email'],
                    'cin' => 'AB' . str_pad($userData['ppr'], 6, '0', STR_PAD_LEFT),
                    'gsm' => '06' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'adresse' => 'Rabat, Maroc',
                    'rib' => 'MA' . str_pad($userData['ppr'], 2, '0', STR_PAD_LEFT) . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT) . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                    'grade_id' => $grade->id,
                    'echelle_id' => $echelle->id,
                    'corps' => 'support',
                ]
            );

            // Create user_settings entry
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

        $this->command->info('Users created successfully!');
        $this->command->info('Total users: ' . (count($users) + 2)); // +2 for admin and manager
        $this->command->info('Admins: 1');
        $this->command->info('Managers (Chefs): ' . count(array_filter($users, fn($u) => $u['is_chef'])));
        $this->command->info('Regular employees: ' . count(array_filter($users, fn($u) => !$u['is_chef'])));
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Entite;
use App\Models\Grade;
use App\Models\Echelle;
use App\Models\UserInfo;
use App\Models\UserSetting;
use App\Models\Parcours;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class EntityUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entites = Entite::all();
        
        if ($entites->isEmpty()) {
            $this->command->warn('No entities found. Please run EntiteSeeder first.');
            return;
        }

        // Get or create roles
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // Get or create echelle
        $echelle = Echelle::first();
        if (!$echelle) {
            $echelle = Echelle::create([
                'name' => 'Echelle 1',
            ]);
        }

        // Get grades
        $grades = Grade::all();
        if ($grades->isEmpty()) {
            $grade = Grade::create([
                'name' => 'Technicien 3eme',
                'echelle_id' => $echelle->id,
            ]);
            $grades = collect([$grade]);
        }

        $totalUsers = 0;
        $pprCounter = 1000; // Start from 1000 to avoid conflicts

        foreach ($entites as $entite) {
            $this->command->info("Creating 2 users for entity: {$entite->name} (ID: {$entite->id})");
            
            // Create 2 users for this entity
            for ($i = 1; $i <= 2; $i++) {
                $ppr = str_pad($pprCounter++, 6, '0', STR_PAD_LEFT);
                
                // Generate random Moroccan name
                $fname = $this->getRandomFirstName();
                $lname = $this->getRandomLastName();
                
                // Generate unique email (use PPR to ensure uniqueness)
                $emailBase = strtolower($fname . '.' . $lname);
                $email = $emailBase . '.' . $ppr . '@anef.ma';
                
                // Ensure email is unique
                $emailCounter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $emailBase . '.' . $ppr . '.' . $emailCounter . '@anef.ma';
                    $emailCounter++;
                }
                
                // First user is chef, others are regular employees
                $isChef = ($i === 1);
                $role = $isChef ? 'manager' : 'user';
                $poste = $this->getRandomPoste($isChef);
                
                // Create user
                $user = User::updateOrCreate(
                    ['ppr' => $ppr],
                    [
                        'fname' => $fname,
                        'lname' => $lname,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'is_active' => true,
                        'is_deleted' => false,
                    ]
                );

                // Assign role
                if (!$user->hasRole($role)) {
                    $user->assignRole($role);
                }

                // Get random grade
                $grade = $grades->random();

                // Create parcours entry (using DB to bypass model validation during seeding)
                DB::table('parcours')->updateOrInsert(
                    [
                        'ppr' => $user->ppr,
                        'entite_id' => $entite->id,
                    ],
                    [
                        'poste' => $poste,
                        'date_debut' => Carbon::now()->subYears(rand(1, 5))->subMonths(rand(0, 11))->format('Y-m-d'),
                        'date_fin' => null, // Active position
                        'grade_id' => $grade->id,
                        'reason' => $this->getRandomReason(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // If user is chef, set chef_ppr in entites table
                if ($isChef) {
                    $entite->update(['chef_ppr' => $user->ppr]);
                }

                // Create user_info entry
                UserInfo::updateOrCreate(
                    ['ppr' => $user->ppr],
                    [
                        'email' => $email,
                        'cin' => 'AB' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'gsm' => '06' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                        'adresse' => $this->getRandomAddress(),
                        'rib' => 'MA' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT) . 
                                str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT) . 
                                str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                        'grade_id' => $grade->id,
                        'echelle_id' => $echelle->id,
                        'corps' => $this->getRandomCorps(),
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

                $totalUsers++;
            }
        }

        $this->command->info("✅ Successfully created {$totalUsers} users across " . $entites->count() . " entities");
        $this->command->info("   - 2 users per entity");
        $this->command->info("   - 1 chef per entity");
        $this->command->info("   - 1 regular employee per entity");
    }

    /**
     * Get a random first name (Moroccan names)
     */
    private function getRandomFirstName(): string
    {
        $firstNames = [
            'Ahmed', 'Mohamed', 'Hassan', 'Youssef', 'Omar', 'Karim', 'Amine', 'Bilal', 'Tarik', 'Rachid',
            'Fatima', 'Aicha', 'Sanae', 'Laila', 'Nadia', 'Salma', 'Imane', 'Houda', 'Souad', 'Khadija',
            'Mehdi', 'Anass', 'Yassine', 'Reda', 'Hamza', 'Zakaria', 'Adil', 'Said', 'Nabil', 'Jamal',
            'Sara', 'Ibtissam', 'Nour', 'Hiba', 'Rim', 'Ines', 'Meriem', 'Yasmine', 'Asmae', 'Hafsa'
        ];
        
        return $firstNames[array_rand($firstNames)];
    }

    /**
     * Get a random last name (Moroccan surnames)
     */
    private function getRandomLastName(): string
    {
        $lastNames = [
            'Alami', 'Bennani', 'Idrissi', 'Lahlou', 'Tazi', 'Mansouri', 'Berrada', 'Chraibi', 'Fassi', 'Amrani',
            'Bensaid', 'El Fassi', 'Bouazza', 'Kettani', 'Sefrioui', 'Alaoui', 'Bennouna', 'Ghannam', 'El Ouazzani', 'Cherkaoui',
            'El Amrani', 'Bouaziz', 'El Malki', 'Bennani', 'El Fassi', 'Alaoui', 'Bensaid', 'Tazi', 'Idrissi', 'Lahlou',
            'Mansouri', 'Berrada', 'Chraibi', 'Fassi', 'Amrani', 'Bensaid', 'El Fassi', 'Bouazza', 'Kettani', 'Sefrioui'
        ];
        
        return $lastNames[array_rand($lastNames)];
    }

    /**
     * Get a random poste name
     */
    private function getRandomPoste(bool $isChef = false): string
    {
        if ($isChef) {
            $chefPostes = [
                'Directeur Général',
                'Secrétaire Général',
                'Chef de Direction',
                'Chef de Département',
                'Chef de Service',
                'Directeur Regionale',
                'Directeur Provincial',
                'Chef de Secteur',
                'Chef de Centre',
            ];
            return $chefPostes[array_rand($chefPostes)];
        }

        $postes = [
            'Directeur Général',
            'Secrétaire Général',
            'Chef de Direction',
            'Chef de Département',
            'Chef de Service',
            'Collaborateur',
            'Directeur Regionale',
            'Directeur Provincial',
            'Chef de Secteur',
            'Chef de Centre',
            
        ];
        
        return $postes[array_rand($postes)];
    }

    /**
     * Get a random reason
     */
    private function getRandomReason(): string
    {
        $reasons = [
            'Recrutement',
            'Mutation',
            'Chargé de Mission',
            'Promotion',
            'Affectation',
            'Nomination',
            'Transfert',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    /**
     * Get a random address
     */
    private function getRandomAddress(): string
    {
        $cities = [
            'Rabat', 'Casablanca', 'Fès', 'Marrakech', 'Tanger', 'Meknès', 'Agadir', 'Oujda', 'Kénitra', 'Tétouan'
        ];
        
        return $cities[array_rand($cities)] . ', Maroc';
    }

    /**
     * Get a random corps (must be 'forestier' or 'support' - enum values)
     */
    private function getRandomCorps(): string
    {
        $corps = ['forestier', 'support'];
        return $corps[array_rand($corps)];
    }
}


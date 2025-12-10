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

class CollaborateurRhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Creating Collaborateur RH Users ===');

        // Find the entity "Service de la Gestion Prévisionnelle des Effectifs et des Compétences"
        $entity = Entite::where('code', 'A3-112')
            ->orWhere('name', 'like', '%Gestion Prévisionnelle des Effectifs%')
            ->orWhere('name', 'like', '%Gestion des Effectifs et Compétences%')
            ->first();

        if (!$entity) {
            $this->command->error('Entity "Service de la Gestion Prévisionnelle des Effectifs et des Compétences" not found!');
            $this->command->info('Available entities with "Effectifs" or "Compétences":');
            Entite::where('name', 'like', '%Effectifs%')
                ->orWhere('name', 'like', '%Compétences%')
                ->get()
                ->each(function($e) {
                    $this->command->line("  - {$e->name} (Code: {$e->code}, ID: {$e->id})");
                });
            return;
        }

        $this->command->info("✓ Found entity: {$entity->name} (Code: {$entity->code}, ID: {$entity->id})");

        // Create or get the "Collaborateur Rh" role
        $collaborateurRhRole = Role::firstOrCreate(
            ['name' => 'Collaborateur Rh', 'guard_name' => 'web']
        );
        $this->command->info("✓ Role 'Collaborateur Rh' ready");

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

        // Define test users for Collaborateur RH
        $testUsers = [
            [
                'ppr' => '001201',
                'fname' => 'Ahmed',
                'lname' => 'Alaoui',
                'poste' => 'Collaborateur RH',
            ],
            [
                'ppr' => '001202',
                'fname' => 'Fatima',
                'lname' => 'Benali',
                'poste' => 'Collaborateur RH',
            ],
            [
                'ppr' => '001203',
                'fname' => 'Mohamed',
                'lname' => 'Idrissi',
                'poste' => 'Collaborateur RH Senior',
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($testUsers as $userData) {
            $ppr = $userData['ppr'];
            $email = strtolower($userData['fname'] . '.' . $userData['lname']) . '@anef.ma';

            // Ensure email is unique
            $emailCounter = 1;
            $originalEmail = $email;
            while (User::where('email', $email)->where('ppr', '!=', $ppr)->exists()) {
                $email = strtolower($userData['fname'] . '.' . $userData['lname']) . '.' . $emailCounter . '@anef.ma';
                $emailCounter++;
            }

            // Create or update user
            $user = User::updateOrCreate(
                ['ppr' => $ppr],
                [
                    'fname' => $userData['fname'],
                    'lname' => $userData['lname'],
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            );

            if ($user->wasRecentlyCreated) {
                $createdCount++;
                $this->command->info("  ✓ Created user: {$user->fname} {$user->lname} (PPR: {$user->ppr})");
            } else {
                $this->command->info("  → Found existing user: {$user->fname} {$user->lname} (PPR: {$user->ppr})");
            }

            // Assign Collaborateur Rh role
            if (!$user->hasRole('Collaborateur Rh')) {
                $user->assignRole($collaborateurRhRole);
                $this->command->info("    ✓ Assigned 'Collaborateur Rh' role");
            }

            // Get random grade
            $grade = $grades->random();

            // Check if user already has an active parcours in this entity
            $existingParcoursInEntity = Parcours::where('ppr', $user->ppr)
                ->where('entite_id', $entity->id)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->first();

            if ($existingParcoursInEntity) {
                // Update existing parcours
                $existingParcoursInEntity->poste = $userData['poste'];
                $existingParcoursInEntity->save();
                $updatedCount++;
                $this->command->info("    ✓ Updated existing parcours");
            } else {
                // Check if user has an active parcours in another entity
                $activeParcoursElsewhere = Parcours::where('ppr', $user->ppr)
                    ->where('entite_id', '!=', $entity->id)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->first();

                if ($activeParcoursElsewhere) {
                    // Close the existing parcours
                    $activeParcoursElsewhere->date_fin = Carbon::now()->subDay();
                    $activeParcoursElsewhere->save();
                    
                    // Remove chef status from entity if user was chef
                    $oldEntite = \App\Models\Entite::find($activeParcoursElsewhere->entite_id);
                    if ($oldEntite && $oldEntite->chef_ppr === $user->ppr) {
                        $oldEntite->update(['chef_ppr' => null]);
                    }
                    
                    $this->command->info("    ✓ Closed existing parcours in another entity");
                }

                // Create new parcours entry using DB to bypass validation
                DB::table('parcours')->insert([
                    'ppr' => $user->ppr,
                    'entite_id' => $entity->id,
                    'poste' => $userData['poste'],
                    'date_debut' => Carbon::now()->subYears(rand(1, 3))->subMonths(rand(0, 11))->format('Y-m-d'),
                    'date_fin' => null, // Active position
                    'grade_id' => $grade->id,
                    'reason' => 'Affectation en tant que Collaborateur RH',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdCount++;
                $this->command->info("    ✓ Created parcours entry as Collaborateur RH");
            }

            // Create or update user_info entry
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

        // Also check for existing users in this entity and mark them as collaborateur RH if they have the role
        $existingUsers = User::whereHas('parcours', function($query) use ($entity) {
            $query->where('entite_id', $entity->id)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })->whereHas('roles', function($query) {
            $query->where('name', 'Collaborateur Rh');
        })->get();

        foreach ($existingUsers as $user) {
            $parcours = Parcours::where('ppr', $user->ppr)
                ->where('entite_id', $entity->id)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->first();

            // User already has the role, no need to update parcours
            $this->command->info("  ✓ User {$user->fname} {$user->lname} already has the Collaborateur Rh role");
        }

        $this->command->info("\n=== Summary ===");
        $this->command->info("Created/Updated users: " . ($createdCount + $updatedCount));
        $this->command->info("Entity: {$entity->name}");
        $this->command->info("All users have the 'Collaborateur Rh' role");
        $this->command->info("\n✓ Seeding completed successfully!");
    }

    /**
     * Get random address
     */
    private function getRandomAddress(): string
    {
        $streets = ['Avenue Mohammed V', 'Boulevard Hassan II', 'Rue Allal Ben Abdellah', 'Avenue des FAR', 'Rue Zerktouni'];
        $cities = ['Rabat', 'Casablanca', 'Fès', 'Marrakech', 'Tanger'];
        
        return $streets[array_rand($streets)] . ', ' . rand(1, 200) . ', ' . $cities[array_rand($cities)];
    }
}

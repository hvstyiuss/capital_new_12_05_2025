<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entite;
use App\Models\EntiteInfo;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Parcours;
use App\Models\Grade;
use App\Models\Echelle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class OrganigrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = storage_path('app/organigramme.csv');
        
        // If CSV is not in storage, try Downloads folder
        if (!file_exists($csvPath)) {
            $csvPath = base_path('individual.csv');
        }
        
        // If still not found, try the Downloads folder path
        if (!file_exists($csvPath)) {
            $csvPath = 'C:\Users\ghann\Downloads\individual.csv';
        }

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found. Please place the CSV file at: storage/app/organigramme.csv");
            return;
        }

        $this->command->info("Reading CSV file: {$csvPath}");

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error("Could not open CSV file.");
            return;
        }

        // Skip header row
        $header = fgetcsv($file);
        
        // Get or create default grade and echelle
        $echelle = Echelle::first() ?? Echelle::create(['name' => 'Echelle 1']);
        $grade = Grade::first() ?? Grade::create(['name' => 'Technicien 3eme', 'echelle_id' => $echelle->id]);

        // Get or create manager role for responsables
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        $entities = [];
        $rowCount = 0;

        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;
            
            if (count($row) < 4) {
                continue;
            }

            $code = trim($row[0], '"');
            $nom = trim($row[1], '"');
            $responsable = trim($row[2], '"');
            $description = trim($row[3], '"');

            // Skip if code or nom is empty
            if (empty($code) || empty($nom)) {
                continue;
            }

            // Create or update entity (preserve date_debut if exists)
            $entite = Entite::firstOrNew(['code' => $code]);
            $entite->name = $nom;
            if (!$entite->date_debut) {
                $entite->date_debut = Carbon::now()->subYears(rand(1, 5));
            }
            $entite->save();

            // Determine type based on entity name
            $type = 'central'; // Default to central
            $nomUpper = strtoupper($nom);
            
            // Check if it's a regional entity
            if (
                strpos($nomUpper, 'DIRECTION REGIONALE') !== false ||
                strpos($nomUpper, 'DIRECTIONS REGIONALES') !== false ||
                strpos($nomUpper, 'DIRECTION PROVINCIALE') !== false ||
                (strpos($nomUpper, 'SERVICE') !== false && (
                    strpos($nomUpper, 'REGIONAL') !== false ||
                    strpos($code, '-B') !== false // Services have codes like 01-B10, 02-B20, etc.
                ))
            ) {
                $type = 'regional';
            }
            
            // Create or update entite_info
            EntiteInfo::updateOrCreate(
                ['entite_id' => $entite->id],
                [
                    'description' => $description ?: null,
                    'type' => $type,
                ]
            );

            // If there's a responsable, create user and parcours
            if (!empty($responsable) && strtoupper($responsable) !== 'NULL') {
                // Normalize responsable name (remove extra spaces, handle prefixes)
                $responsableNormalized = trim($responsable);
                
                // Parse responsable name (format: "LASTNAME FIRSTNAME" or "LASTNAME")
                $nameParts = explode(' ', $responsableNormalized, 2);
                $lname = trim($nameParts[0]);
                $fname = count($nameParts) > 1 ? trim($nameParts[1]) : '';

                // Try to find existing user by name (case-insensitive)
                // Also try to match by removing common prefixes (EL, BEN, etc.)
                $user = null;
                $lnameClean = preg_replace('/^(EL|BEN|AL|OUL|AIT|MOULAY|SIDI)\s+/i', '', $lname);
                
                // Generate potential email to check for duplicates
                $baseEmail = $fname 
                    ? strtolower($fname . '.' . $lname)
                    : strtolower($lname);
                $baseEmail = preg_replace('/[^a-z0-9.]/', '', $baseEmail);
                $potentialEmail = $baseEmail . '@anef.ma';
                
                // First, try to find by email (most reliable)
                $user = User::where('email', $potentialEmail)->first();
                
                // If not found by email, try by name
                if (!$user) {
                    if ($fname) {
                        // Try exact match first
                        $user = User::whereRaw('LOWER(fname) = ?', [strtolower($fname)])
                            ->whereRaw('LOWER(lname) = ?', [strtolower($lname)])
                            ->first();
                        
                        // Try with cleaned last name
                        if (!$user && $lnameClean !== $lname) {
                            $user = User::whereRaw('LOWER(fname) = ?', [strtolower($fname)])
                                ->whereRaw('(LOWER(lname) = ? OR LOWER(lname) LIKE ?)', [
                                    strtolower($lnameClean),
                                    '%' . strtolower($lnameClean) . '%'
                                ])
                                ->first();
                        }
                    } else {
                        // Try exact match first
                        $user = User::whereRaw('LOWER(fname) = ?', [strtolower($lname)])
                            ->whereNull('lname')
                            ->first();
                        
                        // Try with cleaned name
                        if (!$user && $lnameClean !== $lname) {
                            $user = User::whereRaw('LOWER(fname) = ?', [strtolower($lnameClean)])
                                ->whereNull('lname')
                                ->first();
                        }
                    }
                }

                if (!$user) {
                    // Generate PPR as numbers only (7 digits)
                    // Format: 7 digits (e.g., 2378989)
                    $ppr = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);

                    // Ensure unique PPR
                    while (User::where('ppr', $ppr)->exists()) {
                        $ppr = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
                    }

                    // Generate unique email (reuse baseEmail already calculated)
                    $email = $potentialEmail;
                    
                    // Ensure unique email
                    $emailCounter = 1;
                    while (User::where('email', $email)->exists()) {
                        $email = $baseEmail . $emailCounter . '@anef.ma';
                        $emailCounter++;
                    }

                    // Create user
                    $user = User::create([
                        'ppr' => $ppr,
                        'fname' => $fname ?: $lname,
                        'lname' => $fname ? $lname : null,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'is_active' => true,
                        'is_deleted' => false,
                    ]);
                }

                // Create or update user_info (preserve existing data)
                $userInfo = UserInfo::firstOrNew(['ppr' => $user->ppr]);
                $isNew = !$userInfo->exists;
                
                $userInfo->email = $user->email;
                if ($isNew || empty($userInfo->cin)) {
                    $userInfo->cin = 'AB' . str_pad($user->ppr, 6, '0', STR_PAD_LEFT);
                }
                if ($isNew || empty($userInfo->gsm)) {
                    $userInfo->gsm = '06' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                }
                if ($isNew || empty($userInfo->adresse)) {
                    $userInfo->adresse = 'Rabat, Maroc';
                }
                if ($isNew || empty($userInfo->rib)) {
                    $userInfo->rib = 'MA' . str_pad($user->ppr, 2, '0', STR_PAD_LEFT) . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT) . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
                }
                if ($isNew || !$userInfo->grade_id) {
                    $userInfo->grade_id = $grade->id;
                }
                if ($isNew || !$userInfo->echelle_id) {
                    $userInfo->echelle_id = $echelle->id;
                }
                if ($isNew || empty($userInfo->corps)) {
                    $userInfo->corps = 'support';
                }
                $userInfo->responsable = true;
                $userInfo->save();

                // Assign manager role if not already assigned
                if (!$user->hasRole('manager')) {
                    $user->assignRole('manager');
                }

                // Create or update parcours and set chef_ppr in entites (preserve date_debut if exists)
                // First, check if user has an active parcours in a different entity
                $existingActiveParcours = DB::table('parcours')
                    ->where('ppr', $user->ppr)
                    ->where('entite_id', '!=', $entite->id)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->first();
                
                // If user has an active parcours in another entity, close it first
                if ($existingActiveParcours) {
                    $newStartDate = Carbon::now()->subYears(rand(1, 5));
                    $existingEndDate = $newStartDate->copy()->subDay();
                    DB::table('parcours')
                        ->where('id', $existingActiveParcours->id)
                        ->update([
                            'date_fin' => $existingEndDate->format('Y-m-d'),
                            'updated_at' => now()
                        ]);
                    
                    // Remove chef status from old entity if user was chef
                    $oldEntite = Entite::find($existingActiveParcours->entite_id);
                    if ($oldEntite && $oldEntite->chef_ppr === $user->ppr) {
                        $oldEntite->update(['chef_ppr' => null]);
                    }
                }
                
                // Also check if this entity already has an active chef (different user)
                // If so, close that parcours before creating the new one
                $existingChefEntite = Entite::find($entite->id);
                if ($existingChefEntite && $existingChefEntite->chef_ppr && $existingChefEntite->chef_ppr !== $user->ppr) {
                    // Close the existing chef's parcours
                    $existingChefParcours = DB::table('parcours')
                        ->where('entite_id', $entite->id)
                        ->where('ppr', $existingChefEntite->chef_ppr)
                        ->where(function($query) {
                            $query->whereNull('date_fin')
                                  ->orWhere('date_fin', '>=', now());
                        })
                        ->first();
                    
                    if ($existingChefParcours) {
                        $newStartDate = Carbon::now()->subYears(rand(1, 5));
                        $existingEndDate = $newStartDate->copy()->subDay();
                        DB::table('parcours')
                            ->where('id', $existingChefParcours->id)
                            ->update([
                                'date_fin' => $existingEndDate->format('Y-m-d'),
                                'updated_at' => now()
                            ]);
                    }
                }
                
                // Check if parcours already exists for this user and entity
                $existingParcours = DB::table('parcours')
                    ->where('ppr', $user->ppr)
                    ->where('entite_id', $entite->id)
                    ->first();
                
                $dateDebut = $existingParcours && $existingParcours->date_debut 
                    ? $existingParcours->date_debut 
                    : Carbon::now()->subYears(rand(1, 5))->format('Y-m-d');
                
                $parcoursData = [
                    'poste' => 'Responsable',
                    'date_debut' => $dateDebut,
                    'date_fin' => null,
                    'grade_id' => $grade->id,
                    'updated_at' => now()
                ];
                
                if ($existingParcours) {
                    // Update existing parcours
                    DB::table('parcours')
                        ->where('id', $existingParcours->id)
                        ->update($parcoursData);
                    
                    // Set chef_ppr in entites table
                    $entite->update(['chef_ppr' => $user->ppr]);
                } else {
                    // Insert new parcours using DB to bypass model validation
                    DB::table('parcours')->insert(array_merge($parcoursData, [
                        'ppr' => $user->ppr,
                        'entite_id' => $entite->id,
                        'created_at' => now()
                    ]));
                    
                    // Set chef_ppr in entites table
                    $entite->update(['chef_ppr' => $user->ppr]);
                }

                $this->command->info("Created entity: {$code} - {$nom} with responsable: {$responsable} (PPR: {$user->ppr})");
            } else {
                $this->command->info("Created entity: {$code} - {$nom} (no responsable)");
            }

            $entities[] = $entite;
        }

        fclose($file);

        // Build parent-child relationships based on code structure
        $this->buildHierarchy($entities);

        $this->command->info("Successfully imported {$rowCount} entities from CSV!");
    }

    /**
     * Build parent-child relationships based on code structure
     */
    private function buildHierarchy(array $entities): void
    {
        foreach ($entities as $entite) {
            if (empty($entite->code)) {
                continue;
            }

            // Find parent based on code structure
            // For example: "01-001" parent is "01-000", "01-B10" parent is "01-000"
            $codeParts = explode('-', $entite->code);
            
            if (count($codeParts) >= 2) {
                $parentCode = $codeParts[0] . '-000'; // Regional direction
                
                // For services (B10, B20, etc.), parent is the regional direction
                if (strpos($codeParts[1], 'B') === 0) {
                    $parentCode = $codeParts[0] . '-000';
                }
                // For provincial directions (100, 200, etc.), parent is the regional direction
                elseif (is_numeric($codeParts[1]) && strlen($codeParts[1]) >= 3) {
                    $parentCode = $codeParts[0] . '-000';
                }
                // For other entities, try to find a more specific parent
                else {
                    // Keep regional direction as parent for now
                    $parentCode = $codeParts[0] . '-000';
                }

                if ($parentCode !== $entite->code) {
                    $parent = Entite::where('code', $parentCode)->first();
                    if ($parent) {
                        $entite->parent_id = $parent->id;
                        $entite->save();
                    }
                }
            }
        }
    }
}

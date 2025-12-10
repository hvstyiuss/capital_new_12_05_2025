<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Parcours;
use App\Models\Entite;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParcoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with their entities from parcours
        $userEntites = DB::table('parcours')
            ->join('users', 'parcours.ppr', '=', 'users.ppr')
            ->join('entites', 'parcours.entite_id', '=', 'entites.id')
            ->leftJoin('grades', 'parcours.grade_id', '=', 'grades.id')
            ->select(
                'parcours.ppr',
                'parcours.entite_id',
                'parcours.poste',
                'parcours.date_debut',
                'parcours.date_fin',
                'parcours.grade_id',
                'parcours.reason',
                'entites.name as entite_name'
            )
            ->get();

        foreach ($userEntites as $userEntite) {
            // Create parcours entry from parcours data
            $parcoursData = [
                'poste' => $userEntite->poste,
                'date_debut' => $userEntite->date_debut,
                'date_fin' => $userEntite->date_fin,
            ];
            
            // Add grade_id if column exists
            if (\Schema::hasColumn('parcours', 'grade_id')) {
                $parcoursData['grade_id'] = $userEntite->grade_id;
            }
            
            // Add reason if column exists
            if (\Schema::hasColumn('parcours', 'reason')) {
                $parcoursData['reason'] = $userEntite->reason;
            }
            
            // Use DB operations to bypass model validation during seeding
            $existing = DB::table('parcours')
                ->where('ppr', $userEntite->ppr)
                ->where('entite_id', $userEntite->entite_id)
                ->first();
            
            if ($existing) {
                DB::table('parcours')
                    ->where('id', $existing->id)
                    ->update(array_merge($parcoursData, [
                        'updated_at' => now()
                    ]));
            } else {
                DB::table('parcours')->insert(array_merge($parcoursData, [
                    'ppr' => $userEntite->ppr,
                    'entite_id' => $userEntite->entite_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }

        // Also create some additional parcours entries for demonstration
        $users = User::limit(5)->get();
        $entites = Entite::limit(3)->get();
        $grades = Grade::limit(2)->get();

        if ($users->count() > 0 && $entites->count() > 0) {
            foreach ($users as $index => $user) {
                // Create 2-3 parcours entries per user
                $numEntries = rand(2, 3);
                
                // Get existing active parcours for this user
                $existingActiveParcours = Parcours::where('ppr', $user->ppr)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->first();
                
                for ($i = 0; $i < $numEntries; $i++) {
                    $entite = $entites->random();
                    $grade = $grades->count() > 0 ? $grades->random() : null;
                    
                    // Create dates that don't overlap
                    $startDate = Carbon::now()->subYears(rand(5, 10))->subMonths(rand(0, 11));
                    $endDate = $i < $numEntries - 1 
                        ? $startDate->copy()->addYears(rand(1, 3))
                        : null; // Last entry has no end date (current position)
                    
                    // If this will be an active parcours and user already has one, close the existing one first
                    if ($endDate === null && $existingActiveParcours) {
                        // Set date_fin on existing active parcours to the day before the new one starts
                        $existingEndDate = $startDate->copy()->subDay();
                        DB::table('parcours')
                            ->where('id', $existingActiveParcours->id)
                            ->update([
                                'date_fin' => $existingEndDate->format('Y-m-d'),
                                'updated_at' => now()
                            ]);
                        
                        // Remove chef status from entity if user was chef
                        $oldEntite = Entite::find($existingActiveParcours->entite_id);
                        if ($oldEntite && $oldEntite->chef_ppr === $user->ppr) {
                            $oldEntite->update(['chef_ppr' => null]);
                        }
                        $existingActiveParcours = null; // Reset so we don't try to close it again
                    }
                    
                    $additionalData = [
                        'poste' => $this->getRandomPoste(),
                        'date_debut' => $startDate->format('Y-m-d'),
                        'date_fin' => $endDate ? $endDate->format('Y-m-d') : null,
                    ];
                    
                    // Add grade_id if column exists
                    if (Schema::hasColumn('parcours', 'grade_id')) {
                        $additionalData['grade_id'] = $grade?->id;
                    }
                    
                    // Add reason if column exists
                    if (Schema::hasColumn('parcours', 'reason')) {
                        $additionalData['reason'] = $this->getRandomType();
                    }
                    
                    // Use DB insert to bypass model validation during seeding
                    // Check if parcours already exists
                    $existingParcours = DB::table('parcours')
                        ->where('ppr', $user->ppr)
                        ->where('entite_id', $entite->id)
                        ->first();
                    
                    if ($existingParcours) {
                        // Update existing
                        DB::table('parcours')
                            ->where('id', $existingParcours->id)
                            ->update(array_merge($additionalData, [
                                'updated_at' => now()
                            ]));
                    } else {
                        // Insert new
                        DB::table('parcours')->insert(array_merge($additionalData, [
                            'ppr' => $user->ppr,
                            'entite_id' => $entite->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]));
                        
                        // Update existingActiveParcours reference if we just created an active one
                        if ($endDate === null) {
                            $existingActiveParcours = Parcours::where('ppr', $user->ppr)
                                ->where('entite_id', $entite->id)
                                ->first();
                        }
                    }
                }
            }
        }
    }

    /**
     * Get a random poste name.
     */
    private function getRandomPoste(): string
    {
        $postes = [
            'Chef de Service',
            'Ingénieur Forestier',
            'Technicien Forestier',
            'Agent de Protection',
            'Chef de Division',
            'Directeur Adjoint',
            'Chef de Projet',
            'Collaborateur',
            'Superviseur',
            'Assistant',
        ];
        
        return $postes[array_rand($postes)];
    }

    /**
     * Get a random type.
     */
    private function getRandomType(): string
    {
        $types = [
            'Recrutement',
            'Mutation',
            'Chargé de Mission',
            'Promotion',
            'Affectation',
        ];
        
        return $types[array_rand($types)];
    }
}

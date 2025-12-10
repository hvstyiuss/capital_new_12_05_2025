<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Entite;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserEntiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $entites = Entite::all();
        $grades = Grade::all();

        if ($users->isEmpty() || $entites->isEmpty()) {
            $this->command->warn('No users or entities found. Skipping parcours seeding.');
            return;
        }

        foreach ($users as $user) {
            // Assign each user to 1-2 entities
            $numEntites = rand(1, min(2, $entites->count()));
            $selectedEntites = $entites->random($numEntites);
            
            foreach ($selectedEntites as $index => $entite) {
                $grade = $grades->isNotEmpty() ? $grades->random() : null;
                
                // Create dates - first entity starts earlier, subsequent ones start after previous ends
                $startDate = Carbon::now()->subYears(rand(2, 5))->subMonths(rand(0, 11));
                
                // If this is not the first entity for this user, start after previous one
                if ($index > 0) {
                    $previousEnd = DB::table('parcours')
                        ->where('ppr', $user->ppr)
                        ->orderBy('date_fin', 'desc')
                        ->value('date_fin');
                    
                    if ($previousEnd) {
                        $startDate = Carbon::parse($previousEnd)->addDays(1);
                    }
                }
                
                // End date is null for current position, or set for past positions
                $endDate = ($index === $numEntites - 1) 
                    ? null 
                    : $startDate->copy()->addYears(rand(1, 3))->subDays(1);
                
                DB::table('parcours')->updateOrInsert(
                    [
                        'ppr' => $user->ppr,
                        'entite_id' => $entite->id,
                    ],
                    [
                        'poste' => $this->getRandomPoste(),
                        'date_debut' => $startDate->format('Y-m-d'),
                        'date_fin' => $endDate ? $endDate->format('Y-m-d') : null,
                        'grade_id' => $grade?->id,
                        'reason' => $this->getRandomType(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
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

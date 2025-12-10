<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SoldeCongeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active users
        $users = User::where('is_active', true)
            ->where('is_deleted', false)
            ->get();

        $currentYear = Carbon::now()->year;
        $soldeActuel = 22; // Fixed solde actuel for all users

        $this->command->info("Creating annual leave balances (solde_actuel: {$soldeActuel} days) for {$users->count()} users...");

        $created = 0;
        $updated = 0;

        foreach ($users as $user) {
            // Generate random solde_precedent (between 0 and 44 days)
            $randomSoldePrecedent = rand(0, 44);
            
            // Check if record already exists
            $exists = DB::table('solde_conges')
                ->where('ppr', $user->ppr)
                ->where('type', 'Congé Administratif Annuel')
                ->exists();

            $updateData = [
                'solde_precedent' => $randomSoldePrecedent,
                'solde_fix' => $soldeActuel,
                'updated_at' => Carbon::now(),
            ];

            // Add solde_actuel if column exists
            if (Schema::hasColumn('solde_conges', 'solde_actuel')) {
                $updateData['solde_actuel'] = $soldeActuel;
            }

            // Add annee if column exists
            if (Schema::hasColumn('solde_conges', 'annee')) {
                $updateData['annee'] = $currentYear;
            }

            if ($exists) {
                // Update existing record
                DB::table('solde_conges')
                    ->where('ppr', $user->ppr)
                    ->where('type', 'Congé Administratif Annuel')
                    ->update($updateData);
                $updated++;
            } else {
                // Create new record
                $insertData = [
                    'ppr' => $user->ppr,
                    'type' => 'Congé Administratif Annuel',
                    'solde_precedent' => $randomSoldePrecedent,
                    'solde_fix' => $soldeActuel,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                
                // Add solde_actuel if column exists
                if (Schema::hasColumn('solde_conges', 'solde_actuel')) {
                    $insertData['solde_actuel'] = $soldeActuel;
                }
                
                // Add annee if column exists
                if (Schema::hasColumn('solde_conges', 'annee')) {
                    $insertData['annee'] = $currentYear;
                }
                
                DB::table('solde_conges')->insert($insertData);
                $created++;
            }
        }

        $this->command->info("Successfully processed leave balances:");
        $this->command->info("  - Created: {$created}");
        $this->command->info("  - Updated: {$updated}");
        $this->command->info("  - Total: " . ($created + $updated));
    }
}

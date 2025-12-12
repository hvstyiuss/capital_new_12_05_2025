<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class UpdateSoldeCongesForAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solde:update-all {--solde-precedent=22} {--solde-fix=22}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update solde_conges for all users to have 44 days total (22 precedent + 22 fix)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $soldePrecedent = (int) $this->option('solde-precedent');
        $soldeFix = (int) $this->option('solde-fix');
        $soldeActuel = $soldePrecedent + $soldeFix;
        $currentYear = Carbon::now()->year;

        $this->info("Updating solde_conges for all users...");
        $this->info("  - solde_precedent: {$soldePrecedent} days");
        $this->info("  - solde_fix: {$soldeFix} days");
        $this->info("  - solde_actuel: {$soldeActuel} days (total)");

        // Get all active users
        $users = User::where('is_active', true)
            ->where('is_deleted', false)
            ->get();

        $this->info("Found {$users->count()} active users.");

        $created = 0;
        $updated = 0;

        foreach ($users as $user) {
            // Check if record already exists
            $exists = DB::table('solde_conges')
                ->where('ppr', $user->ppr)
                ->where('type', 'Congé Administratif Annuel')
                ->where('annee', $currentYear)
                ->exists();

            $updateData = [
                'solde_precedent' => $soldePrecedent,
                'solde_fix' => $soldeFix,
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
                    ->where('annee', $currentYear)
                    ->update($updateData);
                $updated++;
            } else {
                // Create new record
                $insertData = [
                    'ppr' => $user->ppr,
                    'type' => 'Congé Administratif Annuel',
                    'solde_precedent' => $soldePrecedent,
                    'solde_fix' => $soldeFix,
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

        $this->info("Successfully processed leave balances:");
        $this->info("  - Created: {$created}");
        $this->info("  - Updated: {$updated}");
        $this->info("  - Total: " . ($created + $updated));
        $this->info("All users now have {$soldeActuel} days total ({$soldePrecedent} precedent + {$soldeFix} fix).");

        return Command::SUCCESS;
    }
}




<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Parcours;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixDuplicateActiveParcours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parcours:fix-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix users with multiple active parcours by keeping only the most recent one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing duplicate active parcours...');
        
        // Get all users
        $users = User::all();
        $fixedCount = 0;
        
        foreach ($users as $user) {
            // Get all active parcours for this user
            $activeParcours = Parcours::where('ppr', $user->ppr)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->orderBy('date_debut', 'desc')
                ->get();
            
            // If user has more than one active parcours, fix it
            if ($activeParcours->count() > 1) {
                $this->warn("User {$user->ppr} ({$user->fname} {$user->lname}) has {$activeParcours->count()} active parcours");
                
                // Keep the most recent one (first in the sorted list)
                $mostRecent = $activeParcours->first();
                
                // Set date_fin for all other active parcours
                foreach ($activeParcours->skip(1) as $olderParcours) {
                    // Set date_fin to the day before the most recent one's date_debut
                    $newDateFin = $mostRecent->date_debut->copy()->subDay();
                    
                    // Use DB update to bypass model validation
                    \DB::table('parcours')
                        ->where('id', $olderParcours->id)
                        ->update([
                            'date_fin' => $newDateFin->format('Y-m-d'),
                            'updated_at' => now()
                        ]);
                    
                    $this->line("  - Fixed parcours ID {$olderParcours->id}: set date_fin to {$newDateFin->format('Y-m-d')}");
                }
                
                $fixedCount++;
            }
        }
        
        $this->info("Fixed {$fixedCount} users with duplicate active parcours");
        $this->info('Done!');
        
        return 0;
    }
}

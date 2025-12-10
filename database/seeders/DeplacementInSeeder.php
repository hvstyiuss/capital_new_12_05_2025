<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deplacement;
use App\Models\DeplacementIn;
use App\Models\DeplacementPeriode;
use App\Models\User;
use App\Models\Entite;
use App\Models\EntiteInfo;
use App\Models\Parcours;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeplacementInSeeder extends Seeder
{
    public function run(): void
    {
        // Get all periods
        $periodes = DeplacementPeriode::all();
        
        if ($periodes->isEmpty()) {
            $this->command->warn('No deplacement periods found. Please run DeplacementPeriodeSeeder first.');
            return;
        }

        // Get some users with active parcours
        $users = User::whereHas('parcours', function($query) {
            $query->where(function($q) {
                $q->whereNull('date_fin')
                  ->orWhere('date_fin', '>=', now());
            });
        })
        ->with(['parcours' => function($query) {
            $query->where(function($q) {
                $q->whereNull('date_fin')
                  ->orWhere('date_fin', '>=', now());
            });
        }])
        ->limit(20)
        ->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users with active parcours found.');
            return;
        }

        // Get entities with type (central or regional)
        $entites = Entite::whereHas('entiteInfo', function($query) {
            $query->whereIn('type', ['central', 'regional']);
        })
        ->with('entiteInfo')
        ->get();

        if ($entites->isEmpty()) {
            $this->command->warn('No entities with type found.');
            return;
        }

        $objetOptions = [
            'Mission de formation',
            'Mission de contrôle',
            'Mission d\'inspection',
            'Mission de coordination',
            'Mission de suivi',
            'Mission d\'évaluation',
            'Mission de supervision',
        ];

        $moisOptions = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        $annee = now()->year;

        foreach ($periodes as $periode) {
            // Create deplacements for 10-15 users per period
            $usersForPeriod = $users->random(min(15, $users->count()));
            
            foreach ($usersForPeriod as $user) {
                $currentParcours = $user->parcours->first();
                
                if (!$currentParcours) {
                    continue;
                }

                $entite = $entites->where('id', $currentParcours->entite_id)->first();
                
                if (!$entite) {
                    continue;
                }

                // Create deplacement
                $deplacement = Deplacement::create([
                    'ppr' => $user->ppr,
                    'date_debut' => Carbon::now()->subDays(rand(1, 90)),
                    'date_fin' => Carbon::now()->subDays(rand(1, 30)),
                    'nbr_jours' => rand(1, 10),
                    'echelle_tarifs_id' => null, // Can be set if echelle_tarifs table exists
                    'somme' => rand(500, 50000) + (rand(0, 99) / 100), // Random amount between 500 and 50000
                    'annee' => $annee,
                    'type_in_out' => 'in',
                ]);

                // Create deplacement_in
                DeplacementIn::create([
                    'deplacement_id' => $deplacement->id,
                    'objet' => $objetOptions[array_rand($objetOptions)],
                    'mois' => $moisOptions[array_rand($moisOptions)],
                    'deplacement_periode_id' => $periode->id,
                ]);
            }
        }

        $this->command->info('DeplacementIn seeder completed successfully.');
    }
}




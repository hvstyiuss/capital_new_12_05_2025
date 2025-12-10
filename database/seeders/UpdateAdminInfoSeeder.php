<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Parcours;
use App\Models\Entite;
use App\Models\Grade;
use App\Models\UserInfo;
use App\Models\Echelle;
use Carbon\Carbon;

class UpdateAdminInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('ppr', '001')->first();
        
        if (!$admin) {
            $this->command->warn('Admin user (PPR: 001) not found. Please run UserSeeder first.');
            return;
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
                'name' => 'Technicien 4eme',
                'echelle_id' => $echelle->id,
            ]);
        }

        // Get or create an entity for the admin (preferably a central/administrative entity)
        // Try to find a central entity first, or create one if none exists
        $entite = Entite::where('type', 'central')
            ->orWhere('entity_type', 'central')
            ->orWhere(function($query) {
                $query->where('name', 'LIKE', '%ADMINISTRATION%')
                      ->orWhere('name', 'LIKE', '%CENTRAL%')
                      ->orWhere('name', 'LIKE', '%SIEGE%');
            })
            ->first();

        // If no suitable entity found, get the first entity or create a default one
        if (!$entite) {
            $entite = Entite::first();
            
            if (!$entite) {
                // Create a default central entity
                $entite = Entite::create([
                    'code' => '00-000',
                    'name' => 'Administration Centrale',
                    'type' => 'central',
                    'date_debut' => Carbon::now()->subYears(5),
                    'lieu_affectation' => 'Rabat',
                    'lieu_direction' => 'Rabat',
                ]);
            } else {
                // Update the entity to have lieu_affectation if it doesn't
                if (!$entite->lieu_affectation) {
                    $entite->update([
                        'lieu_affectation' => 'Rabat',
                    ]);
                }
            }
        } else {
            // Ensure the entity has lieu_affectation
            if (!$entite->lieu_affectation) {
                $entite->update([
                    'lieu_affectation' => 'Rabat',
                ]);
            }
        }

        // Create or update UserInfo with GSM and RIB
        UserInfo::updateOrCreate(
            ['ppr' => $admin->ppr],
            [
                'email' => $admin->email,
                'cin' => 'AB000001',
                'gsm' => '0612345678',
                'adresse' => 'Rabat, Maroc',
                'rib' => 'MA0100000000000000000000000',
                'grade_id' => $grade->id,
                'echelle_id' => $echelle->id,
                'corps' => 'support',
            ]
        );

        // Create or update Parcours entry for admin
        $parcours = Parcours::updateOrCreate(
            [
                'ppr' => $admin->ppr,
                'entite_id' => $entite->id,
            ],
            [
                'poste' => 'Administrateur',
                'date_debut' => Carbon::now()->subYears(5),
                'date_fin' => null, // Active position
                'grade_id' => $grade->id,
            ]
        );

        // Set a chef for the entity if it doesn't have one
        // Use the manager user (PPR: 002) as chef if available, otherwise leave it
        if (!$entite->chef_ppr) {
            $manager = User::where('ppr', '002')->first();
            if ($manager) {
                $entite->update(['chef_ppr' => $manager->ppr]);
            }
        }

        $this->command->info('Admin user information updated successfully!');
        $this->command->info('GSM: 0612345678');
        $this->command->info('RIB: MA0100000000000000000000000');
        $this->command->info('Entité: ' . $entite->name);
        $this->command->info('Ville d\'affectation: ' . ($entite->lieu_affectation ?? 'Rabat'));
    }
}


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Echelle;
use App\Models\EchelleTarif;

class EchelleTarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all echelles
        $echelles = Echelle::all();

        if ($echelles->isEmpty()) {
            $this->command->warn('No echelles found. Please run EchelleSeeder first.');
            return;
        }

        // Default montant_deplacement values for each echelle
        // You can adjust these values as needed
        $montants = [
            '6' => 150.00,
            '7' => 180.00,
            '8' => 220.00,
            '9' => 260.00,
            '10' => 300.00,
            '11' => 350.00,
            'HE' => 400.00,
        ];

        // Default max_jours for each echelle
        $maxJours = [
            '6' => 30,
            '7' => 30,
            '8' => 30,
            '9' => 30,
            '10' => 30,
            '11' => 30,
            'HE' => 30,
        ];

        $created = 0;
        $updated = 0;

        foreach ($echelles as $echelle) {
            $echelleName = $echelle->name;
            $montant = $montants[$echelleName] ?? 200.00; // Default to 200 if not specified
            $maxJoursValue = $maxJours[$echelleName] ?? 30; // Default to 30 if not specified

            // Create tarif for 'in' type
            $tarifIn = EchelleTarif::updateOrCreate(
                [
                    'echelle_id' => $echelle->id,
                    'type_in_out_mission' => 'in',
                ],
                [
                    'montant_deplacement' => $montant,
                    'max_jours' => $maxJoursValue,
                ]
            );

            if ($tarifIn->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            // Create tarif for 'out' type (same montant by default, can be adjusted)
            $tarifOut = EchelleTarif::updateOrCreate(
                [
                    'echelle_id' => $echelle->id,
                    'type_in_out_mission' => 'out',
                ],
                [
                    'montant_deplacement' => $montant, // You can set different values for 'out' if needed
                    'max_jours' => $maxJoursValue,
                ]
            );

            if ($tarifOut->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $this->command->info("Echelle {$echelleName}: Created/Updated tarifs (in: {$montant} DH, out: {$montant} DH, max_jours: {$maxJoursValue})");
        }

        $this->command->info("EchelleTarifSeeder completed!");
        $this->command->info("Created: {$created} tarifs");
        $this->command->info("Updated: {$updated} tarifs");
    }
}


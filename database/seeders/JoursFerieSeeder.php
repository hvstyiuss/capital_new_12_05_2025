<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JoursFerie;
use App\Models\TypeJoursFerie;
use Carbon\Carbon;

class JoursFerieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalType = TypeJoursFerie::where('name', 'National')->first();
        $religieuxType = TypeJoursFerie::where('name', 'Religieux')->first();
        
        $currentYear = now()->year;
        
        $joursFeries = [
            // National holidays
            ['date' => "{$currentYear}-01-01", 'name' => 'Jour de l\'An', 'type' => $nationalType],
            ['date' => "{$currentYear}-01-11", 'name' => 'Manifeste de l\'Indépendance', 'type' => $nationalType],
            ['date' => "{$currentYear}-05-01", 'name' => 'Fête du Travail', 'type' => $nationalType],
            ['date' => "{$currentYear}-07-30", 'name' => 'Fête du Trône', 'type' => $nationalType],
            ['date' => "{$currentYear}-08-14", 'name' => 'Allégeance Oued Eddahab', 'type' => $nationalType],
            ['date' => "{$currentYear}-08-20", 'name' => 'Révolution du Roi et du Peuple', 'type' => $nationalType],
            ['date' => "{$currentYear}-08-21", 'name' => 'Fête de la Jeunesse', 'type' => $nationalType],
            ['date' => "{$currentYear}-11-06", 'name' => 'Marche Verte', 'type' => $nationalType],
            ['date' => "{$currentYear}-11-18", 'name' => 'Fête de l\'Indépendance', 'type' => $nationalType],
        ];
        
        foreach ($joursFeries as $jour) {
            JoursFerie::updateOrCreate(
                ['date' => $jour['date']],
                [
                    'name' => $jour['name'],
                    'type_jours_ferie_id' => $jour['type']?->id,
                ]
            );
        }
    }
}

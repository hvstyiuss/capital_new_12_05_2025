<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entite;
use App\Models\EntiteInfo;

class EntiteInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all entities
        $entites = Entite::all();
        
        foreach ($entites as $entite) {
            EntiteInfo::updateOrCreate(
                ['entite_id' => $entite->id],
                [
                    'description' => "Description de l'entité {$entite->name}",
                ]
            );
        }
    }
}

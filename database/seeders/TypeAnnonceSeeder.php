<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeAnnonce;

class TypeAnnonceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'nom' => 'Information',
                'description' => 'Annonces informatives générales',
                'couleur' => '#007bff',
                'is_active' => true,
            ],
            [
                'nom' => 'Urgent',
                'description' => 'Annonces urgentes nécessitant une attention immédiate',
                'couleur' => '#dc3545',
                'is_active' => true,
            ],
            [
                'nom' => 'Réunion',
                'description' => 'Annonces concernant les réunions',
                'couleur' => '#28a745',
                'is_active' => true,
            ],
            [
                'nom' => 'Formation',
                'description' => 'Annonces concernant les formations',
                'couleur' => '#ffc107',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            TypeAnnonce::updateOrCreate(
                ['nom' => $type['nom']],
                $type
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CritereSeeder extends Seeder
{
    public function run(): void
    {
        $criteres = [
            ['category' => 'Discipline', 'nom' => 'Ponctualité'],
            ['category' => 'Productivité', 'nom' => 'Rendement'],
            ['category' => 'Comportement', 'nom' => 'Esprit d\'équipe'],
        ];

        $categories = DB::table('categories')->pluck('id','nom');
        foreach ($criteres as $i => $c) {
            $categoryId = $categories[$c['category']] ?? null;
            if ($categoryId) {
                DB::table('criteres')->updateOrInsert(
                    ['category_id' => $categoryId, 'nom' => $c['nom']],
                    ['category_id' => $categoryId, 'nom' => $c['nom'], 'ordre' => $i + 1]
                );
            }
        }
    }
}





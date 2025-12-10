<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeplacementPeriodeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Trimestre 4'] as $name) {
            DB::table('deplacement_periodes')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}




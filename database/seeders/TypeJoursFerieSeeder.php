<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeJoursFerieSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['National','Religieux'] as $name) {
            DB::table('type_jours_feries')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}





<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeExcepSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Mariage','Décès','Naissance'] as $name) {
            DB::table('type_exceps')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}





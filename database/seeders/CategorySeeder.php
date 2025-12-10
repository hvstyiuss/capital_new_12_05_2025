<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Discipline','Productivité','Comportement'] as $index => $name) {
            DB::table('categories')->updateOrInsert(['nom' => $name], ['nom' => $name, 'ordre' => $index + 1]);
        }
    }
}





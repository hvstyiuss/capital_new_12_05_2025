<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EchelleSeeder extends Seeder
{
    public function run(): void
    {
        $items = ['6','7','8','9','10','11','HE'];
        foreach ($items as $name) {
            DB::table('echelles')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}





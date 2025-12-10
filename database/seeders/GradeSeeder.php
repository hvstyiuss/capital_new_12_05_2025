<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        // Get echelles to assign to grades
        $echelles = DB::table('echelles')->get()->keyBy('name');
        
        // Define grades with their corresponding echelle names
        $grades = [
            'Technicien 4eme' => '8',
            'Technicien 3eme' => '9',
            'Technicien 2eme' => '10',
            'Technicien 1ere' => '11'
        ];
        
        foreach ($grades as $name => $echelleName) {
            $echelleId = $echelles->get($echelleName)?->id;
            
            DB::table('grades')->updateOrInsert(
                ['name' => $name],
                [
                    'name' => $name,
                    'echelle_id' => $echelleId
                ]
            );
        }
    }
}





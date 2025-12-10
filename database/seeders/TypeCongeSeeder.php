<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeConge;

class TypeCongeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'annuel'],
            ['name' => 'maladie'],
            ['name' => 'exceptionnel'],
        ];

        foreach ($types as $type) {
            TypeConge::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('TypeConge types seeded successfully.');
    }
}



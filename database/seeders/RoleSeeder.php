<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Base application roles
        $roles = [
            'admin',
            'manager',
            'user',
            // HR-specific roles
            'Collaborateur Rh',
            'super Collaborateur Rh',
            // Organizational functions/roles
            'directeur general',
            'secretaire general',
            'chef de direction',
            'chef de Departement',
            'chef de service',
            'collaborateur',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}





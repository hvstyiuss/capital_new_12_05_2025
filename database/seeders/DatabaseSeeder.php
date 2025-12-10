<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Roles and Permissions (must be first)
            RoleSeeder::class,
            PermissionSeeder::class,
            
            // Basic reference data
            EchelleSeeder::class,
            GradeSeeder::class,
            TypeExcepSeeder::class,
            TypeJoursFerieSeeder::class,
            TypeAnnonceSeeder::class,
            DeplacementPeriodeSeeder::class,
            DeplacementInSeeder::class,
            
            // Evaluation related
            CategorySeeder::class,
            CritereSeeder::class,
            OptionEvaluationSeeder::class,
            
            // Organizational structure
            EntiteInfoSeeder::class,
            
            // Users and their info
            UserSeeder::class,
            UserInfoSeeder::class,
            UserSettingSeeder::class,
            
            // User-Entity relationships (must be after users and entites)
            //UserEntiteSeeder::class,
            
            // Career paths (must be after parcours)
            ParcoursSeeder::class,
            
            // Holidays
            JoursFerieSeeder::class,
            

            //OrganigrammeSeeder::class,
            SoldeCongeSeeder::class,
            
            // Organigramme import (CSV)
            // OrganigrammeSeeder::class, // Uncomment to import from CSV
            
            // Current Entities Seeder (contains current entities and entite_infos from database)
            CurrentEntitiesSeeder::class,
            
            // Entity Users Seeder (creates 20 users per entity)
            EntityUsersSeeder::class, // Uncomment to create 20 users for each entity
            
            // Assign RH Roles (must be after users are created)
            AssignRhRolesSeeder::class,
        ]);
    }
}

 
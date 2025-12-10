<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Entite;
use App\Models\Parcours;
use Spatie\Permission\Models\Role;

class SuperCollaborateurRhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Creating Super Collaborateur Rh Role ===');

        // Create or get the "super Collaborateur Rh" role
        $superCollaborateurRhRole = Role::firstOrCreate(
            ['name' => 'super Collaborateur Rh', 'guard_name' => 'web']
        );
        $this->command->info("✓ Role 'super Collaborateur Rh' created/ready");

        // Find the entity "Service de la Gestion Prévisionnelle des Effectifs et des Compétences"
        $entity = Entite::where('code', 'A3-112')
            ->orWhere('name', 'like', '%Gestion Prévisionnelle des Effectifs%')
            ->orWhere('name', 'like', '%Gestion des Effectifs et Compétences%')
            ->first();

        if (!$entity) {
            $this->command->error('Entity "Service de la Gestion Prévisionnelle des Effectifs et des Compétences" not found!');
            $this->command->info('Available entities with "Effectifs" or "Compétences":');
            Entite::where('name', 'like', '%Effectifs%')
                ->orWhere('name', 'like', '%Compétences%')
                ->get()
                ->each(function($e) {
                    $this->command->line("  - {$e->name} (Code: {$e->code}, ID: {$e->id})");
                });
            return;
        }

        $this->command->info("✓ Found entity: {$entity->name} (Code: {$entity->code}, ID: {$entity->id})");

        // Find users in this entity who have the "Collaborateur Rh" role
        $usersInEntity = User::whereHas('parcours', function($query) use ($entity) {
            $query->where('entite_id', $entity->id)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })->whereHas('roles', function($query) {
            $query->where('name', 'Collaborateur Rh');
        })->get();

        if ($usersInEntity->isEmpty()) {
            $this->command->error('No users with "Collaborateur Rh" role found in this entity!');
            $this->command->info('Trying to find any user in this entity...');
            
            // Try to find any user in this entity
            $anyUser = User::whereHas('parcours', function($query) use ($entity) {
                $query->where('entite_id', $entity->id)
                      ->where(function($q) {
                          $q->whereNull('date_fin')
                            ->orWhere('date_fin', '>=', now());
                      });
            })->first();

            if ($anyUser) {
                $this->command->info("Found user: {$anyUser->fname} {$anyUser->lname} (PPR: {$anyUser->ppr})");
                $this->command->info("Assigning 'super Collaborateur Rh' role to this user...");
                
                if (!$anyUser->hasRole('super Collaborateur Rh')) {
                    $anyUser->assignRole('super Collaborateur Rh');
                    $this->command->info("✓ Successfully assigned 'super Collaborateur Rh' role to {$anyUser->fname} {$anyUser->lname}");
                } else {
                    $this->command->info("✓ User {$anyUser->fname} {$anyUser->lname} already has the 'super Collaborateur Rh' role");
                }
            } else {
                $this->command->error('No users found in this entity at all!');
            }
            return;
        }

        // Assign the role to the first user found
        $user = $usersInEntity->first();
        $this->command->info("Found user: {$user->fname} {$user->lname} (PPR: {$user->ppr})");

        if (!$user->hasRole('super Collaborateur Rh')) {
            $user->assignRole('super Collaborateur Rh');
            $this->command->info("✓ Successfully assigned 'super Collaborateur Rh' role to {$user->fname} {$user->lname}");
        } else {
            $this->command->info("✓ User {$user->fname} {$user->lname} already has the 'super Collaborateur Rh' role");
        }

        $this->command->info("\n=== Summary ===");
        $this->command->info("Role: super Collaborateur Rh");
        $this->command->info("Entity: {$entity->name}");
        $this->command->info("User: {$user->fname} {$user->lname} (PPR: {$user->ppr})");
    }
}

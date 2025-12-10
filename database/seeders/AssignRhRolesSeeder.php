<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRhRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Assigning RH Roles ===');

        // Get or create roles
        $superCollaborateurRhRole = Role::firstOrCreate(
            ['name' => 'super Collaborateur Rh', 'guard_name' => 'web']
        );
        
        $collaborateurRhRole = Role::firstOrCreate(
            ['name' => 'Collaborateur Rh', 'guard_name' => 'web']
        );

        $this->command->info("✓ Roles ready");

        // Find or create user for "super Collaborateur Rh" role
        // Using PPR 001353 (Hafsa Bennani) as mentioned in the summary
        $superRhUser = User::where('ppr', '001353')->first();
        
        if (!$superRhUser) {
            // If user doesn't exist, try to find any user with "Hafsa" or "Bennani" in name
            $superRhUser = User::where('fname', 'like', '%Hafsa%')
                ->orWhere('lname', 'like', '%Bennani%')
                ->first();
        }

        if ($superRhUser) {
            if (!$superRhUser->hasRole('super Collaborateur Rh')) {
                $superRhUser->assignRole($superCollaborateurRhRole);
                $this->command->info("✓ Assigned 'super Collaborateur Rh' role to {$superRhUser->fname} {$superRhUser->lname} (PPR: {$superRhUser->ppr})");
            } else {
                $this->command->info("✓ User {$superRhUser->fname} {$superRhUser->lname} already has 'super Collaborateur Rh' role");
            }
        } else {
            $this->command->warn("⚠ No user found for 'super Collaborateur Rh' role (searched for PPR: 001353 or name containing 'Hafsa'/'Bennani')");
        }

        // Find or create users for "Collaborateur Rh" role
        // Using PPRs from CollaborateurRhSeeder as reference
        $collaborateurRhPprs = ['001201', '001202', '001203'];
        $assignedCount = 0;

        foreach ($collaborateurRhPprs as $ppr) {
            $user = User::where('ppr', $ppr)->first();
            
            if ($user) {
                if (!$user->hasRole('Collaborateur Rh')) {
                    $user->assignRole($collaborateurRhRole);
                    $this->command->info("✓ Assigned 'Collaborateur Rh' role to {$user->fname} {$user->lname} (PPR: {$user->ppr})");
                    $assignedCount++;
                } else {
                    $this->command->info("✓ User {$user->fname} {$user->lname} already has 'Collaborateur Rh' role");
                }
            } else {
                $this->command->warn("⚠ User with PPR {$ppr} not found");
            }
        }

        // If no collaborateur RH users found, try to find any existing users and assign the role
        if ($assignedCount === 0) {
            $this->command->info("Trying to find any existing users to assign 'Collaborateur Rh' role...");
            
            // Try to find users with "Rh" or "RH" in their email or name
            $potentialUsers = User::where('email', 'like', '%rh%')
                ->orWhere('email', 'like', '%RH%')
                ->orWhere('fname', 'like', '%Rh%')
                ->orWhere('lname', 'like', '%Rh%')
                ->limit(3)
                ->get();

            if ($potentialUsers->isEmpty()) {
                // Get first 3 active users
                $potentialUsers = User::where('is_active', true)
                    ->where('is_deleted', false)
                    ->limit(3)
                    ->get();
            }

            foreach ($potentialUsers as $user) {
                if (!$user->hasRole('Collaborateur Rh') && !$user->hasRole('super Collaborateur Rh')) {
                    $user->assignRole($collaborateurRhRole);
                    $this->command->info("✓ Assigned 'Collaborateur Rh' role to {$user->fname} {$user->lname} (PPR: {$user->ppr})");
                    $assignedCount++;
                }
            }
        }

        $this->command->info("\n=== Summary ===");
        $this->command->info("Super Collaborateur Rh: " . ($superRhUser ? "{$superRhUser->fname} {$superRhUser->lname}" : "Not assigned"));
        $this->command->info("Collaborateur Rh users assigned: {$assignedCount}");
        $this->command->info("✓ Seeding completed!");
    }
}









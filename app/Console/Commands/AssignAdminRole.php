<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spatie:assign-admin {ppr?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign admin role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ppr = $this->argument('ppr') ?? $this->ask('Enter user PPR');
        
        $user = User::find($ppr);
        
        if (!$user) {
            $this->error("User with PPR {$ppr} not found!");
            return 1;
        }

        // Create admin role in Spatie if it doesn't exist
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );

        // Assign role to user
        try {
            $user->assignRole('admin');
            $this->info("Admin role assigned successfully to {$user->name} (PPR: {$ppr})");
            
            // Verify
            if ($user->hasRole('admin')) {
                $this->info("✓ Verified: User has admin role");
            } else {
                $this->warn("⚠ Warning: Role assignment may not have worked");
            }
        } catch (\Exception $e) {
            $this->error("Error assigning role: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}


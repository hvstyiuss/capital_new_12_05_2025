<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncRolesToSpatie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spatie:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync roles from old system to Spatie Permission (if old tables still exist)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Synchronizing roles to Spatie Permission...');

        // Check if old tables exist
        if (!Schema::hasTable('user_role') || !Schema::hasTable('roles')) {
            $this->warn('Old role tables do not exist. Migration may have already been completed.');
            return 0;
        }

        // Get all users with their roles from the old system
        $userRoles = DB::table('user_role')
            ->join('users', 'user_role.ppr', '=', 'users.ppr')
            ->join('roles', 'user_role.role_id', '=', 'roles.id')
            ->select('users.ppr', 'roles.name as role_name')
            ->get();

        $this->info('Found ' . $userRoles->count() . ' user-role assignments');

        // Create Spatie roles if they don't exist
        $roleNames = $userRoles->pluck('role_name')->unique();
        foreach ($roleNames as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
            $this->info("Created/verified Spatie role: {$roleName}");
        }

        // Assign Spatie roles to users
        $bar = $this->output->createProgressBar($userRoles->count());
        $bar->start();

        foreach ($userRoles as $userRole) {
            $user = User::find($userRole->ppr);
            if ($user) {
                try {
                    $user->assignRole($userRole->role_name);
                } catch (\Exception $e) {
                    $this->warn("Failed to assign role {$userRole->role_name} to user {$userRole->ppr}: " . $e->getMessage());
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Roles synchronized successfully!');
        
        return 0;
    }
}

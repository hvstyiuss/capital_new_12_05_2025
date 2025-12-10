<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'admin' => ['manage_users','manage_entites','manage_conges'],
            'manager' => ['approve_conges','view_team'],
            'user' => ['create_demande']
        ];

        foreach ($map as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            
            $permissionObjects = [];
            foreach ($perms as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
                $permissionObjects[] = $permission;
            }
            
            // Sync permissions to role
            $role->syncPermissions($permissionObjects);
        }
    }
}





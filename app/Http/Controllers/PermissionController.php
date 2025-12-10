<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }
    
    public function index(Request $request)
    {
        $query = Permission::with('roles');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        $permissions = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());
        
        // Calculate statistics
        $totalPermissions = Permission::count();
        $totalRoleAssociations = Permission::withCount('roles')->get()->sum('roles_count');
        $activePermissions = Permission::has('roles')->count();
        
        return view('permissions.index', compact('permissions', 'totalPermissions', 'totalRoleAssociations', 'activePermissions'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissionCatalog = collect(config('permissions_catalog.permissions', []));

        return view('permissions.create', [
            'roles' => $roles,
            'permissionCatalog' => $permissionCatalog,
        ]);
    }

    public function store(StorePermissionRequest $request)
    {
        $data = $request->validated();
        
        $permission = Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web'
        ]);
        
        if (isset($data['roles'])) {
            // Convert role IDs from the form into Role models for Spatie
            $roles = Role::whereIn('id', $data['roles'])->get();
            $permission->syncRoles($roles);
        }
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission créée avec succès.');
    }

    public function show(Permission $permission)
    {
        $permission->load(['roles' => function($query) {
            $query->withCount('users');
        }]);
        return view('permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        $roles = Role::all();
        $permission->load('roles');
        $permissionCatalog = collect(config('permissions_catalog.permissions', []));

        return view('permissions.edit', [
            'permission' => $permission,
            'roles' => $roles,
            'permissionCatalog' => $permissionCatalog,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $data = $request->validated();
        
        if (isset($data['name'])) {
            $permission->update(['name' => $data['name']]);
        }
        
        if (array_key_exists('roles', $data)) {
            // Convert role IDs into Role models before syncing
            $roles = Role::whereIn('id', $data['roles'] ?? [])->get();
            $permission->syncRoles($roles);
        }
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission mise à jour avec succès.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission supprimée avec succès.');
    }
}





<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\AddUserToRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }
    
    public function index(Request $request)
    {
        $query = Role::withCount('permissions', 'users');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        $roles = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users' => function($query) {
            $query->with('userInfo')->orderBy('fname')->orderBy('lname');
        }]);
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        
        if (isset($data['name'])) {
            $role->update(['name' => $data['name']]);
        }
        
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Add a user to this role.
     */
    public function addUser(AddUserToRoleRequest $request, Role $role)
    {
        $data = $request->validated();

        $user = User::where('ppr', $data['ppr'])->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur introuvable.');
        }

        if ($user->hasRole($role->name)) {
            return redirect()->back()->with('info', 'Cet utilisateur a déjà ce rôle.');
        }

        $user->assignRole($role->name);

        return redirect()->back()->with('success', 'Utilisateur ajouté au rôle avec succès.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
}





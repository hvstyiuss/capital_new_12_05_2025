<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInfo;
use App\Models\Entite;
use App\Models\Grade;
use App\Models\Echelle;
use App\Models\Parcours;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'userInfo']);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ppr', 'like', "%{$search}%")
                  ->orWhere('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('userInfo', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%")
                        ->orWhere('cin', 'like', "%{$search}%")
                        ->orWhere('gsm', 'like', "%{$search}%");
                  });
            });
        }
        
        // Role filter - using Spatie's role scope
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Entity filter (by active parcours entite)
        if ($request->filled('entite_id')) {
            $entiteId = $request->entite_id;
            $query->whereHas('parcours', function ($q) use ($entiteId) {
                $q->where('entite_id', $entiteId)
                  ->where(function ($sub) {
                      $sub->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                  });
            });
        }
        
        $perPage = $request->get('per_page', 20);
        // Validate per_page to prevent abuse
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 20;
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->query());
        $roles = Role::all();
        $entites = Entite::orderBy('name')->get();
        
        // Global user statistics (moved out of Blade)
        $totalUsers = User::where('is_deleted', false)->count();
        $activeUsers = User::where('is_deleted', false)
            ->where('is_active', true)
            ->count();
        $newUsers30d = User::where('is_deleted', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        // If AJAX request, return JSON
        if ($request->ajax() || $request->has('ajax')) {
            $tableHtml = view('users.partials.table', compact('users'))->render();
            $paginationHtml = $users->hasPages() ? view('users.partials.pagination', compact('users'))->render() : '';
            return response()->json([
                'html' => $tableHtml,
                'pagination' => $paginationHtml,
            ]);
        }
        
        return view('users.index', compact(
            'users',
            'roles',
            'entites',
            'totalUsers',
            'activeUsers',
            'newUsers30d'
        ));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ppr' => 'required|string|unique:users,ppr',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return redirect()->route('hr.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Check if current user has permission (admin or Collaborateur Rh)
        $currentUser = auth()->user();
        if (!$currentUser->hasRole('admin') && !$currentUser->hasRole('Collaborateur Rh')) {
            abort(403, 'Accès non autorisé');
        }
        
        $user->load(['roles', 'userInfo.grade', 'entites']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'userInfo.grade', 'userInfo.echelle']);
        $roles = Role::all();
        $grades = Grade::with('echelle')->orderBy('name')->get();
        $echelles = Echelle::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles', 'grades', 'echelles'));
    }

    public function update(Request $request, User $user)
    {
        // If only updating is_active (status toggle), use simplified validation
        // Check if request only contains is_active (for AJAX status toggle requests)
        $requestKeys = array_keys($request->all());
        $isStatusToggleOnly = $request->has('is_active') && 
                              !$request->has('fname') && 
                              !$request->has('lname') && 
                              !$request->has('email') &&
                              !$request->has('password') &&
                              !$request->has('roles');
        
        if ($isStatusToggleOnly) {
            $validated = $request->validate([
                'is_active' => 'required|boolean',
            ]);
            
            $user->update(['is_active' => $validated['is_active']]);
            
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Statut utilisateur mis à jour avec succès.',
                    'user' => [
                        'ppr' => $user->ppr,
                        'is_active' => $user->is_active,
                    ]
                ]);
            }
            
            return redirect()->route('hr.users.index')->with('success', 'Statut utilisateur mis à jour avec succès.');
        }
        
        // Validate user data for full update
        $validated = $request->validate([
            'fname' => 'required|string|max:50',
            'lname' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:users,email,' . $user->ppr . ',ppr',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,name',
            // UserInfo validation
            'cin' => 'nullable|string|max:255',
            'gsm' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'rib' => 'nullable|string|max:255',
            'grade_id' => 'nullable|exists:grades,id',
            'echelle_id' => 'nullable|exists:echelles,id',
            'corps' => 'nullable|in:forestier,support',
            'responsable' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user basic info
        $userData = [
            'fname' => $validated['fname'],
            'lname' => $validated['lname'] ?? null,
            'email' => $validated['email'] ?? null,
        ];

        if (isset($validated['password']) && !empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        if ($request->has('is_active')) {
            $userData['is_active'] = $request->boolean('is_active');
        }

        $user->update($userData);

        // Update roles
        if ($request->has('roles')) {
            $user->syncRoles($validated['roles']);
        }

        // Handle image upload
        $photoPath = null;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->userInfo && $user->userInfo->photo) {
                Storage::disk('public')->delete($user->userInfo->photo);
            }
            $photoPath = $request->file('image')->store('photos', 'public');
        }

        // Update or create UserInfo
        $userInfoData = [
            'ppr' => $user->ppr,
            'email' => $validated['email'] ?? null,
            'cin' => $validated['cin'] ?? null,
            'gsm' => $validated['gsm'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'rib' => $validated['rib'] ?? null,
            'grade_id' => $validated['grade_id'] ?? null,
            'echelle_id' => $validated['echelle_id'] ?? null,
            'corps' => $validated['corps'] ?? null,
            'responsable' => $request->has('responsable') ? $request->boolean('responsable') : false,
        ];

        if ($photoPath) {
            $userInfoData['photo'] = $photoPath;
        }

        UserInfo::updateOrCreate(
            ['ppr' => $user->ppr],
            $userInfoData
        );

        // Return JSON response for AJAX requests
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => [
                    'ppr' => $user->ppr,
                    'is_active' => $user->is_active,
                ]
            ]);
        }

        return redirect()->route('hr.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('hr.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show the transfer form for a user.
     */
    public function showTransfer(User $user)
    {
        // Check if current user has permission
        $currentUser = auth()->user();
        if (!$currentUser->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Accès non autorisé');
        }

        // Get user's current active parcours
        $currentParcours = Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with('entite')
            ->orderBy('date_debut', 'desc')
            ->first();

        // Get all entities for selection, excluding Directeur Général and Secrétaire Général
        $entites = Entite::where(function($query) {
                $query->where('name', '!=', 'Directeur Général')
                      ->where('name', '!=', 'Secrétaire Général')
                      ->where('name', 'not like', '%DIRECTEUR GÉNÉRAL%')
                      ->where('name', 'not like', '%DIRECTEUR GENERAL%')
                      ->where('name', 'not like', '%SECRÉTAIRE GÉNÉRAL%')
                      ->where('name', 'not like', '%SECRETAIRE GENERAL%');
            })
            ->orderBy('name')
            ->get();

        // Get entities that have chefs with their chef PPR (for JavaScript validation)
        $entitiesWithChefs = $entites->filter(function($entite) {
            return !is_null($entite->chef_ppr);
        })->mapWithKeys(function($entite) {
            return [$entite->id => $entite->chef_ppr];
        })->toArray();

        return view('users.transfer', compact('user', 'currentParcours', 'entites', 'entitiesWithChefs'));
    }

    /**
     * Process the user transfer to a new entity.
     */
    public function processTransfer(Request $request, User $user)
    {
        // Check if current user has permission
        $currentUser = auth()->user();
        if (!$currentUser->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'to_entite_id' => 'required|exists:entites,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'role_in_entity' => 'required|in:collaborateur,chef',
            'poste' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:255',
        ]);

        // Check if trying to assign chef to entity that already has one
        if ($validated['role_in_entity'] === 'chef') {
            $targetEntite = Entite::find($validated['to_entite_id']);
            if ($targetEntite && $targetEntite->chef_ppr && $targetEntite->chef_ppr !== $user->ppr) {
                return redirect()->back()
                    ->withErrors(['role_in_entity' => 'Cette entité a déjà un chef. Vous pouvez soit transférer l\'agent en tant que collaborateur, soit utiliser la fonctionnalité d\'échange de chefs.'])
                    ->withInput();
            }
        }

        // Get user's current active parcours
        $currentParcours = Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->orderBy('date_debut', 'desc')
            ->first();

        if (!$currentParcours) {
            return redirect()->back()
                ->withErrors(['error' => 'Aucun parcours actif trouvé pour cet utilisateur.'])
                ->withInput();
        }

        // Check if transferring to the same entity
        if ($currentParcours->entite_id == $validated['to_entite_id']) {
            return redirect()->back()
                ->withErrors(['to_entite_id' => 'L\'utilisateur est déjà dans cette entité.'])
                ->withInput();
        }

        $dateDebut = Carbon::parse($validated['date_debut']);

        // Use transaction to ensure atomicity
        DB::transaction(function() use ($currentParcours, $user, $validated, $dateDebut, $currentUser) {
            // Close the current parcours by setting date_fin to the day before new one starts
            $endDate = $dateDebut->copy()->subDay();
            
            // Only update if the parcours is still active
            if (is_null($currentParcours->date_fin) || $currentParcours->date_fin >= now()) {
                $currentParcours->date_fin = $endDate;
                $currentParcours->save();
                
                // Remove chef status from old entity if user was chef
                $oldEntite = Entite::find($currentParcours->entite_id);
                if ($oldEntite && $oldEntite->chef_ppr === $user->ppr) {
                    $oldEntite->chef_ppr = null;
                    $oldEntite->save();
                }
            }

            // Determine if user will be a chef based on role_in_entity selection
            $isChef = $validated['role_in_entity'] === 'chef';
            
            // Create new parcours entry for the destination entity
            Parcours::create([
                'ppr' => $user->ppr,
                'entite_id' => $validated['to_entite_id'],
                'poste' => $validated['poste'] ?? $currentParcours->poste ?? ($isChef ? 'Chef' : 'Agent'),
                'role' => $validated['role_in_entity'], // Set role (collaborateur or chef)
                'date_debut' => $dateDebut,
                'date_fin' => null, // Active parcours
                'grade_id' => $currentParcours->grade_id, // Preserve grade
                'reason' => $validated['reason'] ?? 'Transfert administratif',
                'created_by_ppr' => $currentUser->ppr, // Track who created the transfer
            ]);
            
            // Set chef_ppr in entites if user is chef
            if ($isChef) {
                $newEntite = Entite::find($validated['to_entite_id']);
                if ($newEntite) {
                    $newEntite->chef_ppr = $user->ppr;
                    $newEntite->save();
                }
            }
        });

        return redirect()->route('hr.users.show', $user)
            ->with('success', 'L\'utilisateur a été transféré avec succès vers la nouvelle entité.');
    }

    /**
     * Show the swap chefs form page.
     */
    public function showSwapChefs()
    {
        // Check if current user has permission
        $currentUser = auth()->user();
        if (!$currentUser->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Accès non autorisé');
        }

        // Get entities with chefs for swap functionality (eager load chef relation)
        $entitesWithChefs = Entite::whereNotNull('chef_ppr')
            ->with('chef')
            ->orderBy('name')
            ->get();

        return view('users.swap-chefs', compact('entitesWithChefs'));
    }

    /**
     * Swap chefs between two entities.
     */
    public function swapChefs(Request $request)
    {
        // Check if current user has permission
        $currentUser = auth()->user();
        if (!$currentUser->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'entity1_id' => 'required|exists:entites,id',
            'entity2_id' => 'required|exists:entites,id|different:entity1_id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $entity1 = Entite::findOrFail($validated['entity1_id']);
        $entity2 = Entite::findOrFail($validated['entity2_id']);

        // Check both entities have chefs
        if (!$entity1->chef_ppr || !$entity2->chef_ppr) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les deux entités doivent avoir un chef pour effectuer un échange.'
                ], 400);
            }
            return back()->withErrors(['error' => 'Les deux entités doivent avoir un chef pour effectuer un échange.'])->withInput();
        }

        $chef1Ppr = $entity1->chef_ppr;
        $chef2Ppr = $entity2->chef_ppr;
        $swapDate = Carbon::parse($validated['date']);

        // Use transaction to ensure atomicity
        try {
            DB::transaction(function() use ($entity1, $entity2, $chef1Ppr, $chef2Ppr, $swapDate, $currentUser) {
                // Get active parcours for both chefs
                $chef1Parcours = Parcours::where('ppr', $chef1Ppr)
                    ->where('entite_id', $entity1->id)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->orderBy('date_debut', 'desc')
                    ->first();

                $chef2Parcours = Parcours::where('ppr', $chef2Ppr)
                    ->where('entite_id', $entity2->id)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->orderBy('date_debut', 'desc')
                    ->first();

                if (!$chef1Parcours || !$chef2Parcours) {
                    throw new \Exception('Impossible de trouver les parcours actifs des chefs.');
                }

                // End current parcours for both chefs
                $endDate = $swapDate->copy()->subDay();
                $chef1Parcours->date_fin = $endDate;
                $chef1Parcours->save();

                $chef2Parcours->date_fin = $endDate;
                $chef2Parcours->save();

                // Remove chef status from both entities
                $entity1->chef_ppr = null;
                $entity1->save();

                $entity2->chef_ppr = null;
                $entity2->save();

                // Create new parcours for chef1 in entity2
                Parcours::create([
                    'ppr' => $chef1Ppr,
                    'entite_id' => $entity2->id,
                    'poste' => $chef2Parcours->poste ?? 'Chef',
                    'role' => 'chef',
                    'date_debut' => $swapDate,
                    'date_fin' => null,
                    'grade_id' => $chef1Parcours->grade_id,
                    'reason' => 'Échange de chefs',
                    'created_by_ppr' => $currentUser->ppr,
                ]);

                // Create new parcours for chef2 in entity1
                Parcours::create([
                    'ppr' => $chef2Ppr,
                    'entite_id' => $entity1->id,
                    'poste' => $chef1Parcours->poste ?? 'Chef',
                    'role' => 'chef',
                    'date_debut' => $swapDate,
                    'date_fin' => null,
                    'grade_id' => $chef2Parcours->grade_id,
                    'reason' => 'Échange de chefs',
                    'created_by_ppr' => $currentUser->ppr,
                ]);

                // Set new chefs in entities
                $entity1->chef_ppr = $chef2Ppr;
                $entity1->save();

                $entity2->chef_ppr = $chef1Ppr;
                $entity2->save();
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Les chefs ont été échangés avec succès.'
                ]);
            }

            return redirect()->route('hr.users.swap-chefs')
                ->with('success', 'Les chefs ont été échangés avec succès.');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'échange: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erreur lors de l\'échange: ' . $e->getMessage()])->withInput();
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parcours;
use App\Models\Entite;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateAgentRequest;
use App\Http\Requests\UpdateAgentStatusRequest;

class AgentController extends Controller
{
    /**
     * Display a listing of agents for the current chef/admin
     */
    public function consulter(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a chef or admin
        $isChef = Entite::where('chef_ppr', $user->ppr)->exists();
        
        if (!$isChef && !$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas accès à cette page.');
        }
        
        // Get entities where user is chef
        $chefEntiteIds = Entite::where('chef_ppr', $user->ppr)
            ->pluck('id');
        
        // Get PPRs of users in these entities (excluding the chef)
        $agentPprs = Parcours::whereIn('entite_id', $chefEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->where('ppr', '!=', $user->ppr)
            ->pluck('ppr')
            ->unique()
            ->toArray();
        
        // If admin, show all users
        if ($user->hasRole('admin')) {
            $query = User::with(['userInfo', 'parcours.entite', 'parcours.grade']);
        } else {
            $query = User::whereIn('ppr', $agentPprs)
                ->with(['userInfo', 'parcours.entite', 'parcours.grade']);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('ppr', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $agents = $query->orderBy('fname')->orderBy('lname')->paginate(15);
        
        return view('agents.consulter', compact('agents'));
    }

    /**
     * Display page to manage agent accounts (Admin only)
     */
    public function gererComptes(Request $request)
    {
        $user = Auth::user();
        
        // Only admin can access this page
        if (!$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas accès à cette page.');
        }
        
        // Admin can see all users
        $query = User::with(['userInfo', 'parcours.entite']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('ppr', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $agents = $query->orderBy('fname')->orderBy('lname')->paginate(15);
        
        return view('agents.gerer-comptes', compact('agents'));
    }

    /**
     * Update agent account status (Admin only)
     */
    public function updateStatus(UpdateAgentStatusRequest $request, User $agent)
    {
        $user = Auth::user();
        
        // Only admin can update agent status
        if (!$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validated();
        $agentService = app(AgentService::class);
        $agentService->updateAgentStatus($agent, $validated['is_active']);
        
        return response()->json([
            'success' => true,
            'message' => $validated['is_active'] ? 'Compte activé avec succès' : 'Compte désactivé avec succès',
            'is_active' => $agent->fresh()->is_active,
        ]);
    }

    /**
     * Update agent basic information (Admin only)
     */
    public function update(UpdateAgentRequest $request, User $agent)
    {
        $user = Auth::user();
        
        // Only admin can update agent information
        if (!$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à cette page.');
        }
        
        $validated = $request->validated();
        $agentService = app(AgentService::class);
        $agentService->updateAgent($agent, $validated);
        
        return redirect()->back()->with('success', 'Informations mises à jour avec succès.');
    }
}

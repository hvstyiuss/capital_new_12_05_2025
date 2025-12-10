<?php

namespace App\Services;

use App\Models\User;
use App\Models\Parcours;
use App\Models\Entite;
use Illuminate\Support\Facades\Auth;

class AgentService
{
    /**
     * Check if user is a chef or admin.
     */
    public function isAuthorized(User $user): bool
    {
        $isChef = Entite::where('chef_ppr', $user->ppr)->exists();

        return $isChef || $user->hasRole('admin');
    }

    /**
     * Get entities where user is chef.
     */
    public function getChefEntiteIds(User $user): array
    {
        return Entite::where('chef_ppr', $user->ppr)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get agent PPRs for a chef.
     */
    public function getAgentPprs(User $user): array
    {
        if ($user->hasRole('admin')) {
            return []; // Admin can see all users
        }

        $chefEntiteIds = $this->getChefEntiteIds($user);
        
        return Parcours::whereIn('entite_id', $chefEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->where('ppr', '!=', $user->ppr)
            ->pluck('ppr')
            ->unique()
            ->toArray();
    }

    /**
     * Check if agent belongs to chef's entities.
     */
    public function agentBelongsToChef(User $chef, User $agent): bool
    {
        if ($chef->hasRole('admin')) {
            return true;
        }

        $agentPprs = $this->getAgentPprs($chef);
        return in_array($agent->ppr, $agentPprs);
    }

    /**
     * Update agent information.
     */
    public function updateAgent(User $agent, array $validated): void
    {
        $agent->update($validated);
    }

    /**
     * Update agent status.
     */
    public function updateAgentStatus(User $agent, bool $isActive): void
    {
        $agent->update(['is_active' => $isActive]);
    }
}














<?php

namespace App\Actions\Parcours;

use App\Models\Parcours;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListMyParcoursAction
{
    /**
     * Get all parcours and current active parcours for the given user.
     *
     * @return array{parcours: Collection<int, Parcours>, currentParcours: ?Parcours}
     */
    public function execute(User $user): array
    {
        $parcours = Parcours::where('ppr', $user->ppr)
            ->with(['entite.parent', 'grade'])
            ->orderBy('date_debut', 'desc')
            ->get();

        $currentParcours = $parcours->first(function ($p) {
            return is_null($p->date_fin) || $p->date_fin >= now();
        });

        return [
            'parcours'        => $parcours,
            'currentParcours' => $currentParcours,
        ];
    }
}






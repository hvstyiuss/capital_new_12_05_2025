<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EchelleTarif;
use App\Models\Echelle;

class EchelleTarifController extends Controller
{
    /**
     * Display a listing of the echelle tarifs.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        // Get all echelle tarifs with their echelle relationship
        $tarifs = EchelleTarif::with('echelle')
            ->orderBy('echelle_id')
            ->orderBy('type_in_out_mission')
            ->get();

        // Group by echelle for better display
        $groupedTarifs = $tarifs->groupBy('echelle_id');

        // Get all echelles for reference
        $echelles = Echelle::orderBy('name')->get();

        return view('echelle-tarifs.index', compact('tarifs', 'groupedTarifs', 'echelles'));
    }

    /**
     * Update an echelle tarif.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'montant_deplacement' => 'required|numeric|min:0',
            'max_jours' => 'required|integer|min:1',
        ]);

        $tarif = EchelleTarif::findOrFail($id);
        $tarif->update([
            'montant_deplacement' => $validated['montant_deplacement'],
            'max_jours' => $validated['max_jours'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tarif mis à jour avec succès',
            'tarif' => [
                'id' => $tarif->id,
                'montant_deplacement' => $tarif->montant_deplacement,
                'max_jours' => $tarif->max_jours,
            ]
        ]);
    }
}


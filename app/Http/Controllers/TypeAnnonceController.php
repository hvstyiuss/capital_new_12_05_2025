<?php

namespace App\Http\Controllers;

use App\Models\TypeAnnonce;
use App\Http\Requests\StoreTypeAnnonceRequest;
use App\Http\Requests\UpdateTypeAnnonceRequest;
use Illuminate\Http\Request;

class TypeAnnonceController extends Controller
{
    /**
     * Display a listing of announcement types.
     * Admin only.
     */
    public function index(Request $request)
    {
        $query = TypeAnnonce::withCount('annonces');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('statut')) {
            $query->where('is_active', $request->statut);
        }
        
        // Has announcements filter
        if ($request->filled('has_annonces')) {
            if ($request->has_annonces == '1') {
                $query->has('annonces');
            } else {
                $query->doesntHave('annonces');
            }
        }
        
        $types = $query->orderBy('nom')->paginate(20)->appends($request->query());
        return view('type-annonces.index', compact('types'));
    }

    /**
     * Show the form for creating a new type.
     * Admin only.
     */
    public function create()
    {
        return view('type-annonces.create');
    }

    /**
     * Store a newly created type.
     * Admin only.
     */
    public function store(StoreTypeAnnonceRequest $request)
    {
        $validated = $request->validated();

        TypeAnnonce::create($validated);

        return redirect()->route('type-annonces.index')
            ->with('success', 'Type d\'annonce créé avec succès.');
    }

    /**
     * Display the specified type.
     * Admin only.
     */
    public function show(TypeAnnonce $typeAnnonce)
    {
        $typeAnnonce->loadCount('annonces');
        return view('type-annonces.show', compact('typeAnnonce'));
    }

    /**
     * Show the form for editing the specified type.
     * Admin only.
     */
    public function edit(TypeAnnonce $typeAnnonce)
    {
        return view('type-annonces.edit', compact('typeAnnonce'));
    }

    /**
     * Update the specified type.
     * Admin only.
     */
    public function update(UpdateTypeAnnonceRequest $request, TypeAnnonce $typeAnnonce)
    {
        $validated = $request->validated();

        $typeAnnonce->update($validated);

        return redirect()->route('type-annonces.index')
            ->with('success', 'Type d\'annonce mis à jour avec succès.');
    }

    /**
     * Remove the specified type.
     * Admin only.
     */
    public function destroy(TypeAnnonce $typeAnnonce)
    {
        // Check if type has announcements
        if ($typeAnnonce->annonces()->count() > 0) {
            return redirect()->route('type-annonces.index')
                ->with('error', 'Impossible de supprimer ce type car il est utilisé par des annonces.');
        }

        $typeAnnonce->delete();

        return redirect()->route('type-annonces.index')
            ->with('success', 'Type d\'annonce supprimé avec succès.');
    }
}


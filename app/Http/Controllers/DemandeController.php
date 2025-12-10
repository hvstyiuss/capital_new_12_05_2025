<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Http\Requests\StoreDemandeRequest;
use App\Http\Requests\UpdateDemandeRequest;
use App\Http\Resources\DemandeResource;
use App\Http\Resources\DemandeCollection;
use App\Services\DemandeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DemandeController extends Controller
{
    protected DemandeService $demandeService;

    public function __construct(DemandeService $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    public function index(Request $request) 
    { 
        $this->authorize('viewAny', Demande::class);

        $query = Demande::with(['user', 'avis']);

        // Filter by user's own demandes unless admin/HR
        if (!$request->user()->hasRole('admin') && 
            !$request->user()->hasRole('Collaborateur Rh') && 
            !$request->user()->hasRole('super Collaborateur Rh')) {
            $query->where('ppr', $request->user()->ppr);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $perPage = $request->get('per_page', 20);
        $demandes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return new DemandeCollection($demandes);
    }

    public function show(Demande $demande) 
    { 
        $this->authorize('view', $demande);
        
        $demande->load(['user', 'avis']);
        
        return new DemandeResource($demande);
    }

    public function store(StoreDemandeRequest $request)
    {
        $this->authorize('create', Demande::class);
        
        $data = $request->validated();
        $demande = $this->demandeService->create($data);
        $demande->load(['user', 'avis']);
        
        return (new DemandeResource($demande))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateDemandeRequest $request, Demande $demande)
    {
        $this->authorize('update', $demande);
        
        $data = $request->validated();
        $demande = $this->demandeService->update($demande, $data);
        $demande->load(['user', 'avis']);
        
        return new DemandeResource($demande);
    }

    public function destroy(Demande $demande)
    {
        $this->authorize('delete', $demande);
        
        $this->demandeService->delete($demande);
        
        return response()->noContent();
    }
}





<?php

namespace App\Http\Controllers;

use App\Models\Entite;
use App\Http\Requests\StoreEntiteRequest;
use App\Http\Requests\UpdateEntiteRequest;
use App\Http\Resources\EntiteResource;
use App\Http\Resources\EntiteCollection;
use App\Services\EntiteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntiteController extends Controller
{
    protected EntiteService $entiteService;

    public function __construct(EntiteService $entiteService)
    {
        $this->entiteService = $entiteService;
    }

    /**
     * Display a listing of entities.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Entite::class);

        $perPage = $request->get('per_page', 20);
        $entites = $this->entiteService->getPaginated($request->all(), (int) $perPage);

        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new EntiteCollection($entites);
        }

        // Web view (for backward compatibility)
        $allEntities = $this->entiteService->getAllForSelect();
        
        // Get all entities for frontend filtering
        $allEntitesData = $this->entiteService->getAllForFrontend();
        
        return view('entities.index', compact('entites', 'allEntities', 'allEntitesData'));
    }

    /**
     * Show users belonging to an entity (active parcours only)
     */
    public function getUsers(Entite $entite)
    {
        $this->authorize('view', $entite);

        // Load entity with relationships
        $entite->load(['entiteInfo', 'parent']);
        
        // Get active parcours for this entity (date_fin is null or in the future)
        $activeParcours = $entite->parcours()
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with(['user.userInfo', 'grade'])
            ->orderBy('date_debut', 'desc')
            ->get();

        // Sort so that chef appears first if present
        if ($entite->chef_ppr) {
            $activeParcours = $activeParcours->sortByDesc(function ($parcours) use ($entite) {
                return $parcours->ppr === $entite->chef_ppr ? 1 : 0;
            })->values();
        }

        return view('entities.users', compact('entite', 'activeParcours'));
    }

    /**
     * Display the specified entity.
     */
    public function show(Entite $entite) 
    { 
        $this->authorize('view', $entite);
        
        $entite->load(['entiteInfo', 'parent', 'children', 'users']);
        
        return new EntiteResource($entite);
    }

    /**
     * Store a newly created entity.
     */
    public function store(StoreEntiteRequest $request)
    {
        $this->authorize('create', Entite::class);
        
        $data = $request->validated();
        $entite = $this->entiteService->create($data);
        $entite->load(['parent', 'children', 'entiteInfo']);
        
        return (new EntiteResource($entite))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified entity.
     */
    public function update(UpdateEntiteRequest $request, Entite $entite)
    {
        $this->authorize('update', $entite);
        
        $data = $request->validated();
        $entite = $this->entiteService->update($entite, $data);
        $entite->load(['parent', 'children', 'entiteInfo']);
        
        // Return JSON response for API/AJAX requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new EntiteResource($entite);
        }
        
        return redirect()->route('entities.index')->with('success', 'Entité mise à jour avec succès.');
    }

    /**
     * Remove the specified entity.
     */
    public function destroy(Entite $entite)
    {
        $this->authorize('delete', $entite);
        
        $this->entiteService->delete($entite);
        
        return response()->noContent();
    }
}





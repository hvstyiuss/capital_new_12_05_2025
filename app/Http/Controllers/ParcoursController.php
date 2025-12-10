<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Parcours;
use App\Models\Entite;
use App\Http\Requests\StoreParcoursRequest;
use App\Http\Requests\UpdateParcoursRequest;
use App\Http\Resources\ParcoursResource;
use App\Http\Resources\ParcoursCollection;
use App\Services\ParcoursService;
use App\Actions\Parcours\ListParcoursAction;
use App\Actions\Parcours\ListMyParcoursAction;
use App\Actions\Parcours\ShowParcoursAction;
use App\Actions\Parcours\CreateParcoursAction;
use App\Actions\Parcours\UpdateParcoursAction;
use App\Actions\Parcours\DeleteParcoursAction;
use Illuminate\Http\Response;

class ParcoursController extends Controller
{
    protected ParcoursService $parcoursService;

    public function __construct(ParcoursService $parcoursService)
    {
        $this->parcoursService = $parcoursService;
    }

    /**
     * Display the parcours page with pagination, search, and filters.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Parcours::class);
        $filters = [
            'search' => $request->get('search'),
            'entite' => $request->get('entite'),
            'status' => $request->get('status'),
            'per_page' => $request->get('per_page', 15),
        ];

        $parcours = app(ListParcoursAction::class)->execute($filters);

        // Statistics
        $totalParcours = Parcours::count();
        $activeParcours = Parcours::where(function($q) {
            $q->whereNull('date_fin')
              ->orWhere('date_fin', '>=', now());
        })->count();
        $chefParcours = Entite::whereNotNull('chef_ppr')->count();
        $totalUsers = User::whereHas('parcours')->distinct('ppr')->count('ppr');

        // Get entities for filter dropdown
        $entites = Entite::orderBy('name')->get();

        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new ParcoursCollection($parcours);
        }

        return view('parcours.index', compact('parcours', 'totalParcours', 'activeParcours', 'chefParcours', 'totalUsers', 'entites'));
    }

    /**
     * Show the current user's own parcours.
     */
    public function myParcours(Request $request)
    {
        $user = auth()->user();

        $result = app(ListMyParcoursAction::class)->execute($user);
        $parcours = $result['parcours'];
        $currentParcours = $result['currentParcours'];

        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return ParcoursResource::collection($parcours);
        }
        
        return view('parcours.my', compact('user', 'parcours', 'currentParcours'));
    }

    /**
     * Display the specified parcours.
     */
    public function show(Parcours $parcours)
    {
        $this->authorize('view', $parcours);

        $parcours = app(ShowParcoursAction::class)->execute($parcours);

        return new ParcoursResource($parcours);
    }

    /**
     * Store a newly created parcours.
     */
    public function store(StoreParcoursRequest $request)
    {
        $this->authorize('create', Parcours::class);
        
        $validated = $request->validated();
        $dto = new \App\DTOs\Parcours\CreateParcoursDTO(
            ppr: $validated['ppr'],
            entiteId: $validated['entite_id'],
            poste: $validated['poste'] ?? null,
            role: $validated['role'] ?? null,
            dateDebut: $validated['date_debut'],
            dateFin: $validated['date_fin'] ?? null,
            gradeId: $validated['grade_id'] ?? null,
            reason: $validated['reason'] ?? null,
            createdByPpr: $validated['created_by_ppr'] ?? null
        );
        
        $parcours = app(CreateParcoursAction::class)->execute($dto);
        $parcours->load(['user.userInfo', 'entite', 'grade']);
        
        return (new ParcoursResource($parcours))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified parcours.
     */
    public function update(UpdateParcoursRequest $request, Parcours $parcours)
    {
        $this->authorize('update', $parcours);
        
        $validated = $request->validated();
        $dto = new \App\DTOs\Parcours\UpdateParcoursDTO(
            ppr: $validated['ppr'] ?? null,
            entiteId: $validated['entite_id'] ?? null,
            poste: $validated['poste'] ?? null,
            role: $validated['role'] ?? null,
            dateDebut: $validated['date_debut'] ?? null,
            dateFin: $validated['date_fin'] ?? null,
            gradeId: $validated['grade_id'] ?? null,
            reason: $validated['reason'] ?? null,
            createdByPpr: $validated['created_by_ppr'] ?? null
        );
        
        $parcours = app(UpdateParcoursAction::class)->execute($parcours, $dto);
        $parcours->load(['user.userInfo', 'entite', 'grade']);
        
        return new ParcoursResource($parcours);
    }

    /**
     * Remove the specified parcours.
     */
    public function destroy(Parcours $parcours)
    {
        $this->authorize('delete', $parcours);

        app(DeleteParcoursAction::class)->execute($parcours);
        
        return response()->noContent();
    }
}

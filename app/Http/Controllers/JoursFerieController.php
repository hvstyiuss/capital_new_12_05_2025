<?php

namespace App\Http\Controllers;

use App\Models\JoursFerie;
use App\Http\Requests\StoreJoursFerieRequest;
use App\Http\Requests\UpdateJoursFerieRequest;
use App\Http\Resources\JoursFerieResource;
use App\Http\Resources\JoursFerieCollection;
use App\Services\JoursFerieService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JoursFerieController extends Controller
{
    protected JoursFerieService $joursFerieService;

    public function __construct(JoursFerieService $joursFerieService)
    {
        $this->joursFerieService = $joursFerieService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', JoursFerie::class);

        $currentYear = $request->get('year', now()->year);
        
        $filters = [
            'year' => $currentYear,
            'type' => $request->get('type'),
            'search' => $request->get('search'),
        ];
        
        $joursFeries = $this->joursFerieService->getAll($filters);
        $availableYears = $this->joursFerieService->getAvailableYears();

        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new JoursFerieCollection($joursFeries);
        }

        return view('jours-feries.index', compact('joursFeries', 'currentYear', 'availableYears'));
    }

    public function show(JoursFerie $joursFerie)
    {
        $this->authorize('view', $joursFerie);
        
        $joursFerie->load('typeJoursFerie');
        
        return new JoursFerieResource($joursFerie);
    }

    public function store(StoreJoursFerieRequest $request)
    {
        $this->authorize('create', JoursFerie::class);
        
        $data = $request->validated();
        $joursFerie = $this->joursFerieService->create($data);
        $joursFerie->load('typeJoursFerie');
        
        return (new JoursFerieResource($joursFerie))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateJoursFerieRequest $request, JoursFerie $joursFerie)
    {
        $this->authorize('update', $joursFerie);
        
        $data = $request->validated();
        $joursFerie = $this->joursFerieService->update($joursFerie, $data);
        $joursFerie->load('typeJoursFerie');
        
        return new JoursFerieResource($joursFerie);
    }

    public function destroy(JoursFerie $joursFerie)
    {
        $this->authorize('delete', $joursFerie);
        
        $this->joursFerieService->delete($joursFerie);
        
        return response()->noContent();
    }
}




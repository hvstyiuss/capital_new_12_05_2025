<?php

namespace App\Http\Controllers;

use App\Models\AvisRetour;
use App\Http\Requests\StoreAvisRetourRequest;
use App\Http\Requests\UpdateAvisRetourRequest;
use App\Http\Resources\AvisRetourResource;
use App\Http\Resources\AvisRetourCollection;
use App\Services\AvisRetourService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvisRetourController extends Controller
{
    protected AvisRetourService $avisRetourService;

    public function __construct(AvisRetourService $avisRetourService)
    {
        $this->avisRetourService = $avisRetourService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AvisRetour::class);

        $query = AvisRetour::with(['avis']);

        // Filter by avis_id
        if ($request->has('avis_id')) {
            $query->where('avis_id', $request->avis_id);
        }

        // Filter by statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $perPage = $request->get('per_page', 20);
        $avisRetours = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return new AvisRetourCollection($avisRetours);
    }

    public function show(AvisRetour $avisRetour)
    {
        $this->authorize('view', $avisRetour);
        
        $avisRetour->load(['avis']);
        
        return new AvisRetourResource($avisRetour);
    }

    public function store(StoreAvisRetourRequest $request)
    {
        $this->authorize('create', AvisRetour::class);

        $data = $request->validated();

        $avisRetour = $this->avisRetourService->create($data);
        $avisRetour->load(['avis']);
        
        return (new AvisRetourResource($avisRetour))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAvisRetourRequest $request, AvisRetour $avisRetour)
    {
        $this->authorize('update', $avisRetour);

        $data = $request->validated();

        $avisRetour = $this->avisRetourService->update($avisRetour, $data);
        $avisRetour->load(['avis']);
        
        return new AvisRetourResource($avisRetour);
    }

    public function destroy(AvisRetour $avisRetour)
    {
        $this->authorize('delete', $avisRetour);
        
        $this->avisRetourService->delete($avisRetour);
        
        return response()->noContent();
    }
}





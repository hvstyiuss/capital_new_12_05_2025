<?php

namespace App\Http\Controllers;

use App\Models\AvisDepart;
use App\Http\Requests\StoreAvisDepartRequest;
use App\Http\Requests\UpdateAvisDepartRequest;
use App\Http\Resources\AvisDepartResource;
use App\Http\Resources\AvisDepartCollection;
use App\Services\AvisDepartService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvisDepartController extends Controller
{
    protected AvisDepartService $avisDepartService;

    public function __construct(AvisDepartService $avisDepartService)
    {
        $this->avisDepartService = $avisDepartService;
    }

    public function index(Request $request) 
    { 
        $this->authorize('viewAny', AvisDepart::class);

        $query = AvisDepart::with(['avis']);

        // Filter by avis_id
        if ($request->has('avis_id')) {
            $query->where('avis_id', $request->avis_id);
        }

        // Filter by statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $perPage = $request->get('per_page', 20);
        $avisDeparts = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return new AvisDepartCollection($avisDeparts);
    }

    public function show(AvisDepart $avisDepart) 
    { 
        $this->authorize('view', $avisDepart);
        
        $avisDepart->load(['avis']);
        
        return new AvisDepartResource($avisDepart);
    }

    public function store(StoreAvisDepartRequest $request)
    {
        $this->authorize('create', AvisDepart::class);
        
        $data = $request->validated();
        $avisDepart = $this->avisDepartService->create($data);
        $avisDepart->load(['avis']);
        
        return (new AvisDepartResource($avisDepart))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAvisDepartRequest $request, AvisDepart $avisDepart)
    {
        $this->authorize('update', $avisDepart);
        
        $data = $request->validated();
        $avisDepart = $this->avisDepartService->update($avisDepart, $data);
        $avisDepart->load(['avis']);
        
        return new AvisDepartResource($avisDepart);
    }

    public function destroy(AvisDepart $avisDepart)
    {
        $this->authorize('delete', $avisDepart);
        
        $this->avisDepartService->delete($avisDepart);
        
        return response()->noContent();
    }
}
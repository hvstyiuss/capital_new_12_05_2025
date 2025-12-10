<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Http\Requests\StoreAvisRequest;
use App\Http\Requests\UpdateAvisRequest;
use App\Http\Resources\AvisResource;
use App\Http\Resources\AvisCollection;
use App\Services\AvisService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvisController extends Controller
{
    protected AvisService $avisService;

    public function __construct(AvisService $avisService)
    {
        $this->avisService = $avisService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Avis::class);

        $query = Avis::with(['user', 'demande']);

        // Filter by user's own avis unless admin/HR
        if (!$request->user()->hasRole('admin') && 
            !$request->user()->hasRole('Collaborateur Rh') && 
            !$request->user()->hasRole('super Collaborateur Rh')) {
            $query->where('ppr', $request->user()->ppr);
        }

        // Filter by demande_id
        if ($request->has('demande_id')) {
            $query->where('demande_id', $request->demande_id);
        }

        $perPage = $request->get('per_page', 20);
        $avis = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return new AvisCollection($avis);
    }

    public function show(Avis $avi)
    {
        $this->authorize('view', $avi);
        
        $avi->load(['user', 'demande']);
        
        return new AvisResource($avi);
    }

    public function store(StoreAvisRequest $request)
    {
        $this->authorize('create', Avis::class);
        
        $data = $request->validated();
        $avis = $this->avisService->create($data);
        $avis->load(['user', 'demande']);
        
        return (new AvisResource($avis))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAvisRequest $request, Avis $avi)
    {
        $this->authorize('update', $avi);
        
        $data = $request->validated();
        $avis = $this->avisService->update($avi, $data);
        $avis->load(['user', 'demande']);
        
        return new AvisResource($avis);
    }

    public function destroy(Avis $avi)
    {
        $this->authorize('delete', $avi);
        
        $this->avisService->delete($avi);
        
        return response()->noContent();
    }
}





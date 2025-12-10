<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Http\Requests\StoreCongeRequest;
use App\Http\Requests\UpdateCongeRequest;
use App\Http\Resources\CongeResource;
use App\Http\Resources\CongeCollection;
use App\Actions\Conge\ListCongesAction;
use App\Actions\Conge\ShowCongeAction;
use App\Actions\Conge\CreateCongeAction;
use App\Actions\Conge\UpdateCongeAction;
use App\Actions\Conge\DeleteCongeAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CongeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Conge::class);

        $perPage = $request->get('per_page', 20);
        $conges = app(ListCongesAction::class)->execute(
            $request->all(),
            (int) $perPage,
            $request->user()
        );

        return new CongeCollection($conges);
    }

    public function show(Conge $conge)
    {
        $this->authorize('view', $conge);

        $conge = app(ShowCongeAction::class)->execute($conge);

        return new CongeResource($conge);
    }

    public function store(StoreCongeRequest $request)
    {
        $this->authorize('create', Conge::class);
        
        $validated = $request->validated();
        $dto = new \App\DTOs\Conge\CreateCongeDTO(
            ppr: $validated['ppr'],
            annee: $validated['annee']
        );
        
        $conge = app(CreateCongeAction::class)->execute($dto);
        $conge->load(['user']);
        
        return (new CongeResource($conge))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCongeRequest $request, Conge $conge)
    {
        $this->authorize('update', $conge);
        
        $validated = $request->validated();
        $dto = new \App\DTOs\Conge\UpdateCongeDTO(
            annee: $validated['annee'] ?? null
        );
        
        $conge = app(UpdateCongeAction::class)->execute($conge, $dto);
        $conge->load(['user']);
        
        return new CongeResource($conge);
    }

    public function destroy(Conge $conge)
    {
        $this->authorize('delete', $conge);

        app(DeleteCongeAction::class)->execute($conge);
        
        return response()->noContent();
    }
}





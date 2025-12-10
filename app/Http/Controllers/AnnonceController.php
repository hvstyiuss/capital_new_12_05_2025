<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Http\Requests\StoreAnnonceRequest;
use App\Http\Requests\UpdateAnnonceRequest;
use App\Http\Resources\AnnonceResource;
use App\Http\Resources\AnnonceCollection;
use App\Actions\Annonce\ListAnnoncesAction;
use App\Actions\Annonce\ShowAnnonceAction;
use App\Actions\Annonce\CreateAnnonceAction;
use App\Actions\Annonce\UpdateAnnonceAction;
use App\Actions\Annonce\DeleteAnnonceAction;
use App\Actions\Annonce\PrepareCreateFormAction;
use App\Actions\Annonce\PrepareEditFormAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnnonceController extends Controller
{
    /**
     * Display a listing of announcements.
     * All authenticated users (user, manager, admin) can view.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Annonce::class);

        $perPage = $request->get('per_page', 12);
        $annonces = app(ListAnnoncesAction::class)->execute($request->all(), (int) $perPage);
        
        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new AnnonceCollection($annonces);
        }
        
        return view('annonces.index', compact('annonces'));
    }

    /**
     * Show the form for creating a new announcement.
     * Admin only.
     */
    public function create()
    {
        $this->authorize('create', Annonce::class);
        
        $data = app(PrepareCreateFormAction::class)->execute();
        
        return view('annonces.create', $data);
    }

    /**
     * Store a newly created announcement.
     * Admin only.
     */
    public function store(StoreAnnonceRequest $request)
    {
        $this->authorize('create', Annonce::class);

        $validated = $request->validated();
        $dto = new \App\DTOs\Annonce\CreateAnnonceDTO(
            ppr: $validated['ppr'] ?? null,
            content: $validated['content'],
            typeAnnonceId: $validated['type_annonce_id'],
            statut: $validated['statut'] ?? null,
            image: $request->file('image'),
            entites: $validated['entites']
        );
        
        $annonce = app(CreateAnnonceAction::class)->execute($dto, $request->user());

        // Return JSON for API requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return (new AnnonceResource($annonce))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        }

        return redirect()->route('annonces.index')
            ->with('success', 'Annonce créée avec succès.');
    }

    /**
     * Display the specified announcement.
     * All authenticated users can view.
     */
    public function show(Request $request, Annonce $annonce)
    {
        $this->authorize('view', $annonce);
        
        $annonce = app(ShowAnnonceAction::class)->execute($annonce);
        
        // Return JSON for API requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new AnnonceResource($annonce);
        }
        
        return view('annonces.show', compact('annonce'));
    }

    /**
     * Show the form for editing the specified announcement.
     * Admin only.
     */
    public function edit(Annonce $annonce)
    {
        $this->authorize('update', $annonce);
        
        $data = app(PrepareEditFormAction::class)->execute($annonce);
        
        return view('annonces.edit', $data);
    }

    /**
     * Update the specified announcement.
     * Admin only.
     */
    public function update(UpdateAnnonceRequest $request, Annonce $annonce)
    {
        $this->authorize('update', $annonce);

        $validated = $request->validated();
        $dto = new \App\DTOs\Annonce\UpdateAnnonceDTO(
            ppr: $validated['ppr'] ?? null,
            content: $validated['content'] ?? null,
            typeAnnonceId: $validated['type_annonce_id'] ?? null,
            statut: $validated['statut'] ?? null,
            image: $request->file('image'),
            entites: $validated['entites'] ?? null
        );
        
        $annonce = app(UpdateAnnonceAction::class)->execute($annonce, $dto, $request->user());

        // Return JSON for API requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new AnnonceResource($annonce);
        }

        return redirect()->route('annonces.index')
            ->with('success', 'Annonce mise à jour avec succès.');
    }

    /**
     * Remove the specified announcement.
     * Admin only.
     */
    public function destroy(Annonce $annonce)
    {
        $this->authorize('delete', $annonce);

        app(DeleteAnnonceAction::class)->execute($annonce);

        return response()->noContent();
    }
}

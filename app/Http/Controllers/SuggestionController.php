<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Http\Requests\StoreSuggestionRequest;
use App\Http\Requests\UpdateSuggestionRequest;
use App\Http\Resources\SuggestionResource;
use App\Http\Resources\SuggestionCollection;
use App\Services\SuggestionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SuggestionController extends Controller
{
    protected SuggestionService $suggestionService;

    public function __construct(SuggestionService $suggestionService)
    {
        $this->suggestionService = $suggestionService;
    }

    /**
     * Display a listing of suggestions.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Suggestion::class);

        $filters = [
            'statut' => $request->get('statut'),
            'ppr' => $request->user()->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']) 
                ? $request->get('ppr') 
                : $request->user()->ppr,
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 15),
        ];

        $suggestions = $this->suggestionService->getAll($filters);

        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new SuggestionCollection($suggestions);
        }

        return view('suggestions.index', compact('suggestions'));
    }

    /**
     * Display the specified suggestion.
     */
    public function show(Suggestion $suggestion)
    {
        $this->authorize('view', $suggestion);
        
        $suggestion->load('user');
        
        return new SuggestionResource($suggestion);
    }

    /**
     * Store a newly created suggestion.
     */
    public function store(StoreSuggestionRequest $request)
    {
        $this->authorize('create', Suggestion::class);

        $data = $request->validated();
        $data['ppr'] = $request->user()->ppr;
        $data['statut'] = 'pending';
        
        $suggestion = $this->suggestionService->create($data);
        $suggestion->load('user');

        // Return JSON for API requests, redirect for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return (new SuggestionResource($suggestion))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        }

        return redirect()->route('suggestions.index')
            ->with('success', 'Votre suggestion a été envoyée avec succès. Nous vous remercions pour votre contribution !');
    }

    /**
     * Update the specified suggestion.
     */
    public function update(UpdateSuggestionRequest $request, Suggestion $suggestion)
    {
        $this->authorize('update', $suggestion);
        
        $data = $request->validated();
        $suggestion = $this->suggestionService->update($suggestion, $data);
        $suggestion->load('user');
        
        return new SuggestionResource($suggestion);
    }

    /**
     * Remove the specified suggestion.
     */
    public function destroy(Suggestion $suggestion)
    {
        $this->authorize('delete', $suggestion);
        
        $this->suggestionService->delete($suggestion);
        
        return response()->noContent();
    }
}


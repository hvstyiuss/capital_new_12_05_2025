<?php

namespace App\Http\Controllers;

use App\Models\NoteAnnuelle;
use App\Models\User;
use App\Http\Requests\StoreNoteAnnuelleRequest;
use App\Http\Requests\UpdateNoteAnnuelleRequest;
use App\Http\Resources\NoteAnnuelleResource;
use App\Http\Resources\NoteAnnuelleCollection;
use App\Services\NoteAnnuelleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NoteAnnuelleController extends Controller
{
    protected NoteAnnuelleService $noteAnnuelleService;

    public function __construct(NoteAnnuelleService $noteAnnuelleService)
    {
        $this->noteAnnuelleService = $noteAnnuelleService;
    }

    /**
     * Display a listing of annual notes.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // For regular users, show only their own notes
        if (!$user->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            $notes = $this->noteAnnuelleService->getByPpr($user->ppr);
        } else {
            $filters = [
                'ppr' => $request->get('ppr'),
                'annee' => $request->get('annee'),
            ];
            $notes = $this->noteAnnuelleService->getAll($filters);
        }
        
        // Get all years with notes, or default to last 2 years if no notes
        $years = $notes->pluck('annee')->unique()->sort()->values();
        if ($years->isEmpty()) {
            $currentYear = date('Y');
            $years = collect([$currentYear - 1, $currentYear]);
        }
        
        // Get user info with grade
        $user->load('userInfo.grade');
        
        // Format data for the table
        $tableData = [];
        $notesByYear = $notes->keyBy('annee');
        
        $tableData[] = [
            'ppr' => $user->ppr,
            'nom_complet' => $user->name,
            'grade' => $user->userInfo->grade->name ?? 'Non défini',
            'notes' => $years->mapWithKeys(function($year) use ($notesByYear) {
                $note = $notesByYear->get($year);
                if (!$note) {
                    return [$year => null];
                }
                
                // Determine observation based on note
                $observation = null;
                if ($note->note == 20) {
                    $observation = 'Excellent';
                } elseif ($note->note >= 18) {
                    $observation = 'Très Bien';
                } elseif ($note->note >= 16) {
                    $observation = 'Bien';
                } elseif ($note->note >= 14) {
                    $observation = 'Assez Bien';
                } elseif ($note->note >= 12) {
                    $observation = 'Passable';
                } else {
                    $observation = 'Insuffisant';
                }
                
                return [$year => [
                    'note' => $note->note,
                    'observation' => $observation,
                ]];
            })->toArray(),
        ];

        // Return JSON for API requests, view for web requests
        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            return new NoteAnnuelleCollection($notes);
        }
        
        return view('notes-annuelles.index', compact('tableData', 'years', 'user'));
    }

    /**
     * Display the specified note annuelle.
     */
    public function show(NoteAnnuelle $noteAnnuelle)
    {
        $this->authorize('view', $noteAnnuelle);
        
        $noteAnnuelle->load('user');
        
        return new NoteAnnuelleResource($noteAnnuelle);
    }

    /**
     * Store a newly created note annuelle.
     */
    public function store(StoreNoteAnnuelleRequest $request)
    {
        $this->authorize('create', NoteAnnuelle::class);
        
        $data = $request->validated();
        $noteAnnuelle = $this->noteAnnuelleService->create($data);
        $noteAnnuelle->load('user');
        
        return (new NoteAnnuelleResource($noteAnnuelle))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified note annuelle.
     */
    public function update(UpdateNoteAnnuelleRequest $request, NoteAnnuelle $noteAnnuelle)
    {
        $this->authorize('update', $noteAnnuelle);
        
        $data = $request->validated();
        $noteAnnuelle = $this->noteAnnuelleService->update($noteAnnuelle, $data);
        $noteAnnuelle->load('user');
        
        return new NoteAnnuelleResource($noteAnnuelle);
    }

    /**
     * Remove the specified note annuelle.
     */
    public function destroy(NoteAnnuelle $noteAnnuelle)
    {
        $this->authorize('delete', $noteAnnuelle);
        
        $this->noteAnnuelleService->delete($noteAnnuelle);
        
        return response()->noContent();
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Mutation;
use App\Models\Entite;
use App\Models\Parcours;
use App\Http\Requests\StoreMutationRequest;
use App\Http\Requests\UpdateMutationRequest;
use App\Http\Requests\ApproveMutationRequest;
use App\Http\Requests\RejectMutationRequest;
use App\Http\Requests\ApproveDestinationReceptionRequest;
use App\Http\Requests\RejectDestinationReceptionRequest;
use App\Http\Resources\MutationResource;
use App\Http\Resources\MutationCollection;
use App\Services\MutationService;
use App\Actions\Mutation\ListMutationsAction;
use App\Actions\Mutation\ShowMutationAction;
use App\Actions\Mutation\CreateMutationAction;
use App\Actions\Mutation\UpdateMutationAction;
use App\Actions\Mutation\DeleteMutationAction;
use App\Actions\Mutation\PrepareCreateFormAction;
use App\Actions\Mutation\ShowTrackingAction;
use App\Actions\Mutation\ApproveMutationAction;
use App\Actions\Mutation\RejectMutationAction;
use App\Actions\Mutation\ApproveDestinationReceptionAction;
use App\Actions\Mutation\RejectDestinationReceptionAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use DomainException;

class MutationController extends Controller
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    /**
     * Display a listing of mutations.
     */
    public function index(Request $request): MutationCollection
    {
        $this->authorize('viewAny', Mutation::class);

        $perPage = $request->get('per_page', 20);
        $mutations = app(ListMutationsAction::class)->execute(
            $request->all(),
            (int) $perPage,
            $request->user()
        );

        return new MutationCollection($mutations);
    }

    /**
     * Display the specified mutation.
     */
    public function show(Mutation $mutation): MutationResource
    {
        $this->authorize('view', $mutation);

        $mutation = app(ShowMutationAction::class)->execute($mutation);

        return new MutationResource($mutation);
    }

    /**
     * Store a newly created mutation.
     */
    public function store(StoreMutationRequest $request): JsonResponse|MutationResource
    {
        $this->authorize('create', Mutation::class);

        $validated = $request->validated();
        $user = $request->user();

        $dto = new \App\DTOs\Mutation\CreateMutationDTO(
            ppr: $validated['ppr'] ?? $user->ppr,
            toEntiteId: $validated['to_entite_id'],
            mutationType: $validated['mutation_type'],
            motif: $validated['motif'],
            motifAutre: $validated['motif_autre'] ?? null
        );

        try {
            $mutation = app(CreateMutationAction::class)->execute($user, $dto);
            $mutation->load(['user', 'toEntite']);

            return (new MutationResource($mutation))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified mutation.
     */
    public function update(UpdateMutationRequest $request, Mutation $mutation): MutationResource
    {
        $this->authorize('update', $mutation);

        $validated = $request->validated();
        $dto = new \App\DTOs\Mutation\UpdateMutationDTO(
            toEntiteId: $validated['to_entite_id'] ?? null,
            mutationType: $validated['mutation_type'] ?? null,
            motif: $validated['motif'] ?? null,
            motifAutre: $validated['motif_autre'] ?? null,
            approvedByCurrentDirection: $validated['approved_by_current_direction'] ?? null,
            approvedByDestinationDirection: $validated['approved_by_destination_direction'] ?? null,
            approvedBySuperCollaborateurRh: $validated['approved_by_super_collaborateur_rh'] ?? null,
            rejectedByCurrentDirection: $validated['rejected_by_current_direction'] ?? null,
            rejectedByDestinationDirection: $validated['rejected_by_destination_direction'] ?? null,
            rejectedBySuperRh: $validated['rejected_by_super_rh'] ?? null,
            rejectionReasonCurrent: $validated['rejection_reason_current'] ?? null,
            rejectionReasonDestination: $validated['rejection_reason_destination'] ?? null,
            rejectionReasonSuperRh: $validated['rejection_reason_super_rh'] ?? null,
            dateDebutAffectation: $validated['date_debut_affectation'] ?? null
        );
        
        $mutation = app(UpdateMutationAction::class)->execute($mutation, $dto);
        $mutation->load(['user', 'toEntite']);

        return new MutationResource($mutation);
    }

    /**
     * Remove the specified mutation.
     */
    public function destroy(Mutation $mutation): Response
    {
        $this->authorize('delete', $mutation);

        app(DeleteMutationAction::class)->execute($mutation);

        return response()->noContent();
    }

    /**
     * Show the form for creating a new mutation (web view).
     */
    public function create(Request $request)
    {
        $user = $request->user();

        $this->authorize('create', Mutation::class);

        // Prevent access if the user already has a pending mutation request
        if ($this->mutationService->hasPendingMutations($user->ppr)) {
            return redirect()
                ->route('mutations.tracking')
                ->with('error', 'Vous avez déjà une demande de mutation en attente. Vous ne pouvez pas créer une nouvelle demande tant que la précédente n\'est pas traitée.');
        }

        $data = app(PrepareCreateFormAction::class)->execute($user);

        return view('mutations.create', $data);
    }

    /**
     * Show the tracking page for user's mutations.
     */
    public function tracking(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', Mutation::class);

        $filters = [
            'year' => $request->get('year', date('Y')),
            'per_page' => $request->get('per_page', 25),
            'search' => $request->get('search', ''),
        ];

        $data = app(ShowTrackingAction::class)->execute($user, $filters);

        return view('mutations.tracking', $data);
    }

    /**
     * Show mutation requests from agents (for chefs/directors).
     */
    public function agentRequests(Request $request)
    {
        $user = $request->user();
        
        // Check if user is a chef or director
        $isChef = $user->isChef();
        $isDirector = method_exists($user, 'isDirectorOfDirection') ? $user->isDirectorOfDirection() : false;
        $isSpecialChef = $this->mutationService->isChefOfSpecialEntity($user);
        
        if (!$isChef && !$isDirector && !$isSpecialChef) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas accès à cette page.');
        }
        
        // Get entities where user is chef
        $chefEntiteIds = Entite::where('chef_ppr', $user->ppr)
            ->pluck('id')
            ->toArray();
        
        // Get all child entities of the chef's entities
        $childEntiteIds = Entite::whereIn('parent_id', $chefEntiteIds)
            ->pluck('id')
            ->toArray();
        
        // Combine chef's direct entities and their child entities
        $allEntiteIds = array_unique(array_merge($chefEntiteIds, $childEntiteIds));
        
        // Get PPRs of users in these entities (excluding the chef)
        $agentPprs = Parcours::whereIn('entite_id', $allEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->where('ppr', '!=', $user->ppr)
            ->pluck('ppr')
            ->unique()
            ->toArray();
        
        // Get mutations from these agents
        $query = Mutation::whereIn('ppr', $agentPprs)
            ->with(['user', 'toEntite'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status if provided
        $status = $request->get('status', '');
        if ($status === 'pending') {
            // Pending: not approved or rejected by current direction
            $query->where(function($q) {
                $q->where('approved_by_current_direction', 0)
                  ->where('rejected_by_current_direction', 0);
            });
        } elseif ($status === 'approved') {
            // Approved by current direction
            $query->where('approved_by_current_direction', 1)
                  ->where('rejected_by_current_direction', 0);
        } elseif ($status === 'rejected') {
            // Rejected by current direction
            $query->where('rejected_by_current_direction', 1);
        }
        
        // Filter by mutation type if provided
        $mutationType = $request->get('mutation_type', '');
        if ($mutationType) {
            $query->where('mutation_type', $mutationType);
        }
        
        // Filter by year if provided
        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }
        
        // Filter by search term if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('fname', 'like', "%{$search}%")
                              ->orWhere('lname', 'like', "%{$search}%")
                              ->orWhere('ppr', 'like', "%{$search}%");
                })
                ->orWhereHas('toEntite', function($entiteQuery) use ($search) {
                    $entiteQuery->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        $perPage = $request->get('per_page', 25);
        $mutations = $query->paginate($perPage);
        
        return view('mutations.agent-requests', [
            'mutations' => $mutations,
            'status' => $status,
            'mutationType' => $mutationType,
            'year' => $request->get('year', ''),
            'search' => $request->get('search', ''),
            'perPage' => $perPage,
        ]);
    }

    /**
     * Approve a mutation request (for directors/chefs).
     */
    public function approve(ApproveMutationRequest $request, Mutation $mutation): JsonResponse|MutationResource
    {
        $user = $request->user();
        
        $this->authorize('approveAsDirection', $mutation);

        $dto = new \App\DTOs\Mutation\ApproveMutationDTO(
            approvalType: $request->input('approval_type')
        );

        try {
            $mutation = app(ApproveMutationAction::class)->execute($mutation, $user, $dto);

            return new MutationResource($mutation);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Reject a mutation request (for directors/chefs).
     */
    public function reject(RejectMutationRequest $request, Mutation $mutation): JsonResponse|MutationResource
    {
        $user = $request->user();
        
        $this->authorize('approveAsDirection', $mutation);

        $dto = new \App\DTOs\Mutation\RejectMutationDTO(
            rejectionType: $request->input('rejection_type'),
            rejectionReason: $request->input('rejection_reason', null)
        );

        try {
            $mutation = app(RejectMutationAction::class)->execute($mutation, $user, $dto);

            return new MutationResource($mutation);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Approve destination reception (for super Collaborateur Rh).
     */
    public function approveDestinationReception(ApproveDestinationReceptionRequest $request, Mutation $mutation): JsonResponse|MutationResource
    {
        $user = $request->user();
        
        $this->authorize('superRh', Mutation::class);

        $validated = $request->validated();
        $dto = new \App\DTOs\Mutation\ApproveDestinationReceptionDTO(
            dateDebutAffectation: $validated['date_debut_affectation']
        );

        try {
            $mutation = app(ApproveDestinationReceptionAction::class)->execute($mutation, $user, $dto);

            return new MutationResource($mutation);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Reject destination reception (for super Collaborateur Rh).
     */
    public function rejectDestinationReception(RejectDestinationReceptionRequest $request, Mutation $mutation): JsonResponse|MutationResource
    {
        $user = $request->user();
        
        $this->authorize('superRh', Mutation::class);

        $validated = $request->validated();
        $dto = new \App\DTOs\Mutation\RejectDestinationReceptionDTO(
            rejectionReasonSuperRh: $validated['rejection_reason_super_rh']
        );

        try {
            $mutation = app(RejectDestinationReceptionAction::class)->execute($mutation, $user, $dto);

            return new MutationResource($mutation);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Show mutations statistics (admin only)
     */
    public function stats()
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        // Total mutations
        $totalMutations = Mutation::count();
        
        // Pending mutations
        $pendingMutations = Mutation::where(function($query) {
            $query->where(function($q) {
                $q->where('mutation_type', 'interne')
                  ->where('approved_by_current_direction', 0)
                  ->where('rejected_by_current_direction', 0);
            })->orWhere(function($q) {
                $q->where('mutation_type', 'externe')
                  ->where(function($sub) {
                      $sub->where('approved_by_current_direction', 0)
                          ->orWhere('approved_by_destination_direction', 0);
                  })
                  ->where('rejected_by_current_direction', 0)
                  ->where('rejected_by_destination_direction', 0);
            });
        })->count();
        
        // Approved mutations
        $approvedMutations = Mutation::where(function($query) {
            $query->where(function($q) {
                $q->where('mutation_type', 'interne')
                  ->where('approved_by_current_direction', 1)
                  ->where('rejected_by_current_direction', 0);
            })->orWhere(function($q) {
                $q->where('mutation_type', 'externe')
                  ->where('approved_by_current_direction', 1)
                  ->where('approved_by_destination_direction', 1)
                  ->where('rejected_by_current_direction', 0)
                  ->where('rejected_by_destination_direction', 0);
            });
        })->count();
        
        // Rejected mutations
        $rejectedMutations = Mutation::where(function($query) {
            $query->where('rejected_by_current_direction', 1)
                  ->orWhere('rejected_by_destination_direction', 1);
        })->count();

        // Mutations by type
        $mutationsByType = DB::table('mutations')
            ->select('mutation_type', DB::raw('count(*) as count'))
            ->groupBy('mutation_type')
            ->get();

        // Mutations by status
        $mutationsByStatus = [
            'En attente' => $pendingMutations,
            'Approuvées' => $approvedMutations,
            'Rejetées' => $rejectedMutations,
        ];

        // Monthly trends (last 6 months)
        $monthlyMutations = [];
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthlyMutations[] = Mutation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Recent mutations (last 10)
        $recentMutations = Mutation::with(['user', 'toEntite', 'user.parcours.entite'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // This year statistics
        $mutationsThisYear = Mutation::whereYear('created_at', now()->year)->count();
        $mutationsThisMonth = Mutation::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Approved mutations this year
        $approvedThisYear = Mutation::where(function($query) {
            $query->where(function($q) {
                $q->where('mutation_type', 'interne')
                  ->where('approved_by_current_direction', 1)
                  ->where('rejected_by_current_direction', 0);
            })->orWhere(function($q) {
                $q->where('mutation_type', 'externe')
                  ->where('approved_by_current_direction', 1)
                  ->where('approved_by_destination_direction', 1)
                  ->where('rejected_by_current_direction', 0)
                  ->where('rejected_by_destination_direction', 0);
            });
        })
        ->whereYear('created_at', now()->year)
        ->count();

        // Rejected mutations this year
        $rejectedThisYear = Mutation::where(function($query) {
            $query->where('rejected_by_current_direction', 1)
                  ->orWhere('rejected_by_destination_direction', 1);
        })
        ->whereYear('created_at', now()->year)
        ->count();

        return view('mutations.stats', compact(
            'totalMutations',
            'pendingMutations',
            'approvedMutations',
            'rejectedMutations',
            'mutationsByType',
            'mutationsByStatus',
            'monthlyMutations',
            'months',
            'recentMutations',
            'mutationsThisYear',
            'mutationsThisMonth',
            'approvedThisYear',
            'rejectedThisYear'
        ));
    }
}

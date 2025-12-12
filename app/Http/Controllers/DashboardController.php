<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Demande;
use App\Models\Entite;
use App\Models\Parcours;
use App\Models\Mutation;
use App\Models\Deplacement;
use App\Models\AvisDepart;
use App\Models\AvisRetour;
use App\Models\DismissedAlert;
use App\Services\NotificationService;
use App\Services\MutationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected MutationService $mutationService;

    public function __construct(MutationService $mutationService)
    {
        $this->mutationService = $mutationService;
    }

    public function index()
    {
        $user = auth()->user();

        // Eager load user relationships for the dashboard (for all roles)
        $user->load([
            'userInfo.grade.Echelle',
            'parcours' => function($query) {
                $query->where(function($q) {
                    $q->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
                })->with(['entite.parent.parent.parent.parent.parent.parent.parent.parent.parent.parent']); // Load parent hierarchy to find direction (up to 10 levels)
            }
        ]);
            
        // Check for approved mutations that need parcours updates
        $this->checkAndUpdateParcoursForApprovedMutations($user);
        
        // Get user's current entity and chef
        // Query database directly to get the most recent active parcours
        $currentParcours = Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with(['entite.parent.parent.parent.parent.parent.parent.parent.parent.parent.parent'])
            ->orderBy('date_debut', 'desc')
            ->first();
            
        // Fallback to most recent parcours if no active one found
        if (!$currentParcours) {
            $currentParcours = Parcours::where('ppr', $user->ppr)
                ->with(['entite.parent.parent.parent.parent.parent.parent.parent.parent.parent.parent'])
                ->orderBy('date_debut', 'desc')
                ->first();
        }
        
        $chefName = null;
        $chefPpr = null;
        $chefEntiteName = null;
        if ($currentParcours && $currentParcours->entite) {
            // Load the entite with its parcours to get the chef
            $entite = $currentParcours->entite;
            
            // Find chef along entity hierarchy using chef_ppr
            $current = $entite;
            $maxDepth = 10;
            $depth = 0;
            
            while ($current && $depth < $maxDepth) {
                if ($current->chef_ppr && $current->chef_ppr !== $user->ppr) {
                    $chefUser = User::where('ppr', $current->chef_ppr)->first();
                    if ($chefUser) {
                        $chefName = $chefUser->fname . ' ' . $chefUser->lname;
                        $chefPpr = $chefUser->ppr;
                        $chefEntiteName = $current->name; // Get the entity name where chef is assigned
                        break;
                    }
                }
                
                if (!$current->parent_id) {
                    break;
                }
                
                if (!$current->relationLoaded('parent')) {
                    $current->load('parent');
                }
                
                $current = $current->parent;
                $depth++;
            }
        }
        
        // Get direction entity using service
        $userDirection = $currentParcours && $currentParcours->entite 
            ? $this->mutationService->getDirectionEntity($currentParcours->entite) 
            : null;
        
        // Check if user is a chef using the User model method
        $isChef = $user->isChef();
        
        $pendingDemandesForChef = [];
        $pendingDemandesCount = 0;
        $pendingMutationsForChef = [];
        $pendingMutationsCount = 0;
        
        if ($isChef) {
            // Get entities where user is chef
            $chefEntiteIds = Entite::where('chef_ppr', $user->ppr)
                ->pluck('id')
                ->toArray();
            
            // Get all descendant entities (children, grandchildren, etc.)
            $allEntiteIds = $chefEntiteIds;
            foreach ($chefEntiteIds as $entiteId) {
                $entite = Entite::find($entiteId);
                if ($entite) {
                    $descendants = $this->getDescendantEntiteIds($entite);
                    $allEntiteIds = array_merge($allEntiteIds, $descendants);
                }
            }
            $allEntiteIds = array_unique($allEntiteIds);
            
            // Get PPRs of users who are CURRENTLY in these entities (active parcours only)
            // Only include users with active parcours (date_fin is null or in the future)
            $userPprs = Parcours::whereIn('entite_id', $allEntiteIds)
                ->where('ppr', '!=', $user->ppr)
                ->where(function($query) {
                    // Only active parcours: no end date OR end date is in the future
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->where(function($query) {
                    // Ensure date_debut is not in the future (parcours has started)
                    $query->whereNull('date_debut')
                          ->orWhere('date_debut', '<=', now());
                })
                ->orderBy('date_debut', 'desc') // Get most recent parcours first
                ->get()
                ->unique('ppr') // Only keep one parcours per user (most recent)
                ->pluck('ppr')
                ->toArray();
            
            // Get pending demandes from these users (avis de départ is pending)
            $pendingDemandesForChef = Demande::whereIn('ppr', $userPprs)
                ->whereHas('avis.avisDepart', function($query) {
                    $query->where('statut', 'pending');
                })
                ->with(['user', 'avis.avisDepart'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            $pendingDemandesCount = Demande::whereIn('ppr', $userPprs)
                ->whereHas('avis.avisDepart', function($query) {
                    $query->where('statut', 'pending');
                })
                ->count();
            
            // Get pending mutations from these users (waiting for chef approval)
            // These are mutations that haven't been approved or rejected by current direction yet
            $pendingMutationsForChef = Mutation::whereIn('ppr', $userPprs)
                ->where(function($query) {
                    // Not approved by current direction
                    $query->where('approved_by_current_direction', false)
                          ->orWhereNull('approved_by_current_direction');
                })
                ->whereNull('approved_by_current_direction_at')
                ->where(function($query) {
                    // Not rejected by current direction
                    $query->where('rejected_by_current_direction', false)
                          ->orWhereNull('rejected_by_current_direction');
                })
                ->whereNull('rejected_by_current_direction_at')
                ->with(['user', 'toEntite'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            $pendingMutationsCount = Mutation::whereIn('ppr', $userPprs)
                ->where(function($query) {
                    // Not approved by current direction
                    $query->where('approved_by_current_direction', false)
                          ->orWhereNull('approved_by_current_direction');
                })
                ->whereNull('approved_by_current_direction_at')
                ->where(function($query) {
                    // Not rejected by current direction
                    $query->where('rejected_by_current_direction', false)
                          ->orWhereNull('rejected_by_current_direction');
                })
                ->whereNull('rejected_by_current_direction_at')
                ->count();
        }
        
        // Check if user has a leave request with today as return date and hasn't declared return yet
        $needsReturnDeclaration = false;
        $returnDeclarationDemande = null;
        
        $today = Carbon::today();
        $demandeWithReturnToday = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) use ($today) {
                $query->whereDate('date_retour', $today)
                      ->where('statut', 'approved'); // Only if avis de départ is validated
            })
            ->whereDoesntHave('avis.avisRetour')
            ->with(['avis.avisDepart'])
            ->first();
        
        if ($demandeWithReturnToday) {
            $needsReturnDeclaration = true;
            $returnDeclarationDemande = $demandeWithReturnToday;
        }
        
        // Get dismissed alert demande IDs for this user
        $dismissedDemandeIds = DismissedAlert::where('ppr', $user->ppr)
            ->where('alert_type', 'status_change')
            ->pluck('demande_id')
            ->toArray();
        
        // Check for recently approved or rejected demandes (within last 7 days)
        $recentStatusChanges = [];
        $recentApproved = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'approved')
                      ->where('updated_at', '>=', Carbon::now()->subDays(7));
            })
            ->whereNotIn('id', $dismissedDemandeIds) // Exclude dismissed alerts
            ->with(['avis.avisDepart'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $recentRejected = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'rejected')
                      ->where('updated_at', '>=', Carbon::now()->subDays(7));
            })
            ->whereNotIn('id', $dismissedDemandeIds) // Exclude dismissed alerts
            ->with(['avis.avisDepart'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Only show if status changed from pending (not if it was already approved/rejected)
        foreach ($recentApproved as $demande) {
            // Check if it was recently changed (within last 24 hours to show prominently)
            if ($demande->updated_at->isToday() || $demande->updated_at->isYesterday()) {
                $recentStatusChanges[] = [
                    'demande' => $demande,
                    'status' => 'approved',
                    'is_recent' => $demande->updated_at->isToday(),
                ];
            }
        }
        
        foreach ($recentRejected as $demande) {
            // Check if it was recently changed (within last 24 hours to show prominently)
            if ($demande->updated_at->isToday() || $demande->updated_at->isYesterday()) {
                $recentStatusChanges[] = [
                    'demande' => $demande,
                    'status' => 'rejected',
                    'is_recent' => $demande->updated_at->isToday(),
                ];
            }
        }
        
        // Check for recent avis de retour declarations from collaborators (for chefs)
        $recentAvisRetourDeclarations = [];
        $dismissedAvisRetourIds = DismissedAlert::where('ppr', $user->ppr)
            ->where('alert_type', 'avis_retour_declaration')
            ->pluck('demande_id')
            ->toArray();

        if ($isChef) {
            // Get entities where user is chef
            $chefEntiteIds = Entite::where('chef_ppr', $user->ppr)
                ->pluck('id')
                ->toArray();
            
            // Get all descendant entities (children, grandchildren, etc.)
            $allEntiteIds = $chefEntiteIds;
            foreach ($chefEntiteIds as $entiteId) {
                $entite = Entite::find($entiteId);
                if ($entite) {
                    $descendants = $this->getDescendantEntiteIds($entite);
                    $allEntiteIds = array_merge($allEntiteIds, $descendants);
                }
            }
            $allEntiteIds = array_unique($allEntiteIds);
            
            // Get PPRs of users who are CURRENTLY in these entities (active parcours only)
            $userPprs = Parcours::whereIn('entite_id', $allEntiteIds)
                ->where('ppr', '!=', $user->ppr)
                ->where(function($query) {
                    // Only active parcours: no end date OR end date is in the future
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->where(function($query) {
                    // Ensure date_debut is not in the future (parcours has started)
                    $query->whereNull('date_debut')
                          ->orWhere('date_debut', '<=', now());
                })
                ->orderBy('date_debut', 'desc') // Get most recent parcours first
                ->get()
                ->unique('ppr') // Only keep one parcours per user (most recent)
                ->pluck('ppr')
                ->toArray();

            // Get recent avis de retour declarations (within last 7 days)
            $recentAvisRetours = Demande::whereIn('ppr', $userPprs)
                ->whereHas('avis.avisRetour', function($query) {
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                })
                ->whereNotIn('id', $dismissedAvisRetourIds)
                ->with(['user', 'avis.avisRetour', 'avis.avisDepart'])
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($recentAvisRetours as $demande) {
                $avisRetour = $demande->avis->avisRetour ?? null;
                if ($avisRetour) {
                    $avisDepart = $demande->avis->avisDepart ?? null;
                    $recentAvisRetourDeclarations[] = [
                        'demande' => $demande,
                        'avis_retour' => $avisRetour,
                        'is_recent' => $avisRetour->created_at->isToday(),
                        'collaborateur_name' => $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A',
                        'date_retour_declaree' => $avisRetour->date_retour_declaree ? $avisRetour->date_retour_declaree->format('d/m/Y') : 'N/A',
                        'nbr_jours_consumes' => $avisRetour->nbr_jours_consumes ?? 0,
                        'date_depart' => $avisDepart && $avisDepart->date_depart ? $avisDepart->date_depart->format('d/m/Y') : 'N/A',
                    ];
                }
            }
        }

        // Check for recent mutation status changes
        $recentMutationChanges = [];
        $dismissedMutationIds = DismissedAlert::where('ppr', $user->ppr)
            ->where('alert_type', 'mutation_status_change')
            ->pluck('demande_id') // Reusing demande_id column for mutation_id
            ->toArray();
        
        // Get approved mutations (show until dismissed, no time limit)
        // Only show mutations that are fully approved including super Collaborateur Rh validation
        $recentApprovedMutations = Mutation::where('ppr', $user->ppr)
            ->where(function($query) {
                // Internal mutations: approved by current direction AND super Collaborateur Rh
                $query->where(function($q) {
                    $q->where('mutation_type', 'interne')
                      ->where('approved_by_current_direction', true)
                      ->where('approved_by_super_collaborateur_rh', true)
                      ->whereNotNull('approved_by_current_direction_at')
                      ->whereNotNull('approved_by_super_collaborateur_rh_at');
                })
                // External mutations: fully approved (both directions AND super Collaborateur Rh)
                ->orWhere(function($q) {
                    $q->where('mutation_type', 'externe')
                      ->where('approved_by_current_direction', true)
                      ->where('approved_by_destination_direction', true)
                      ->where('approved_by_super_collaborateur_rh', true)
                      ->whereNotNull('approved_by_current_direction_at')
                      ->whereNotNull('approved_by_destination_direction_at')
                      ->whereNotNull('approved_by_super_collaborateur_rh_at');
                });
            })
            ->whereNotIn('id', $dismissedMutationIds)
            ->with(['toEntite', 'approvedByCurrentDirection', 'approvedByDestinationDirection', 'approvedBySuperCollaborateurRh'])
            ->orderByRaw('GREATEST(COALESCE(approved_by_super_collaborateur_rh_at, 0), COALESCE(approved_by_current_direction_at, 0), COALESCE(approved_by_destination_direction_at, 0)) DESC')
            ->get();
        
        // Get rejected mutations (show until dismissed, no time limit)
        $recentRejectedMutations = Mutation::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('rejected_by_current_direction', true)
                      ->whereNotNull('rejected_by_current_direction_at');
                })
                ->orWhere(function($q) {
                    $q->where('rejected_by_destination_direction', true)
                      ->whereNotNull('rejected_by_destination_direction_at');
                })
                ->orWhere(function($q) {
                    $q->where('rejected_by_super_rh', true)
                      ->whereNotNull('rejected_by_super_rh_at');
                });
            })
            ->whereNotIn('id', $dismissedMutationIds)
            ->with(['toEntite', 'rejectedByCurrentDirection', 'rejectedByDestinationDirection', 'rejectedBySuperRh'])
            ->orderByRaw('GREATEST(COALESCE(rejected_by_current_direction_at, 0), COALESCE(rejected_by_destination_direction_at, 0), COALESCE(rejected_by_super_rh_at, 0)) DESC')
            ->get();
        
        // Process approved mutations - show all until dismissed (not just today/yesterday)
        foreach ($recentApprovedMutations as $mutation) {
            $approvalDate = $mutation->approved_by_current_direction_at ?? 
                           $mutation->approved_by_destination_direction_at;
            if ($approvalDate) {
                // Get approver name
                $approverName = null;
                if ($mutation->approvedBySuperCollaborateurRh) {
                    $approverName = $mutation->approvedBySuperCollaborateurRh->fname . ' ' . $mutation->approvedBySuperCollaborateurRh->lname;
                } elseif ($mutation->approvedByCurrentDirection) {
                    $approverName = $mutation->approvedByCurrentDirection->fname . ' ' . $mutation->approvedByCurrentDirection->lname;
                } elseif ($mutation->approvedByDestinationDirection) {
                    $approverName = $mutation->approvedByDestinationDirection->fname . ' ' . $mutation->approvedByDestinationDirection->lname;
                }
                
                $recentMutationChanges[] = [
                    'mutation' => $mutation,
                    'status' => 'approved',
                    'is_recent' => $approvalDate->isToday() || $approvalDate->isYesterday(),
                    'to_entite_name' => $mutation->toEntite ? $mutation->toEntite->name : 'N/A',
                    'date_depot' => $mutation->created_at ? $mutation->created_at->format('d/m/Y') : 'N/A',
                    'approver_name' => $approverName,
                ];
            }
        }
        
        // Process rejected mutations - show all until dismissed (not just today/yesterday)
        foreach ($recentRejectedMutations as $mutation) {
            $rejectionDate = $mutation->rejected_by_current_direction_at ?? 
                            $mutation->rejected_by_destination_direction_at ??
                            $mutation->rejected_by_super_rh_at;
            if ($rejectionDate) {
                // Get rejector name
                $rejectorName = null;
                if ($mutation->rejectedBySuperRh) {
                    $rejectorName = $mutation->rejectedBySuperRh->fname . ' ' . $mutation->rejectedBySuperRh->lname;
                } elseif ($mutation->rejectedByCurrentDirection) {
                    $rejectorName = $mutation->rejectedByCurrentDirection->fname . ' ' . $mutation->rejectedByCurrentDirection->lname;
                } elseif ($mutation->rejectedByDestinationDirection) {
                    $rejectorName = $mutation->rejectedByDestinationDirection->fname . ' ' . $mutation->rejectedByDestinationDirection->lname;
                }
                
                $recentMutationChanges[] = [
                    'mutation' => $mutation,
                    'status' => 'rejected',
                    'is_recent' => $rejectionDate->isToday() || $rejectionDate->isYesterday(),
                    'to_entite_name' => $mutation->toEntite ? $mutation->toEntite->name : 'N/A',
                    'date_depot' => $mutation->created_at ? $mutation->created_at->format('d/m/Y') : 'N/A',
                    'rejector_name' => $rejectorName,
                ];
            }
        }
        
        // Get mutations pending super Collaborateur Rh validation
        $pendingSuperCollaborateurRhMutations = [];
        if ($user->hasRole('super Collaborateur Rh')) {
            $dismissedSuperRhMutationIds = DismissedAlert::where('ppr', $user->ppr)
                ->where('alert_type', 'mutation_pending_super_rh')
                ->pluck('demande_id') // Reusing demande_id column for mutation_id
                ->toArray();
            
            $pendingSuperRhMutations = Mutation::where(function($query) {
                // Internal mutations: approved by current direction but not by super Collaborateur Rh
                $query->where(function($q) {
                    $q->where('mutation_type', 'interne')
                      ->where('approved_by_current_direction', true)
                      ->where('approved_by_super_collaborateur_rh', false)
                      ->where('rejected_by_super_rh', false)
                      ->whereNull('rejected_by_current_direction');
                })
                // External mutations - Stage 1: approved by current direction, not sent to destination yet
                ->orWhere(function($q) {
                    $q->where('mutation_type', 'externe')
                      ->where('approved_by_current_direction', true)
                      ->where('sent_to_destination_by_super_rh', false)
                      ->where('rejected_by_super_rh', false)
                      ->where('approved_by_super_collaborateur_rh', false)
                      ->whereNull('rejected_by_current_direction');
                })
                // External mutations - Stage 2: sent to destination and approved, waiting for final validation
                ->orWhere(function($q) {
                    $q->where('mutation_type', 'externe')
                      ->where('approved_by_current_direction', true)
                      ->where('sent_to_destination_by_super_rh', true)
                      ->where('approved_by_destination_direction', true)
                      ->where('approved_by_super_collaborateur_rh', false)
                      ->where('rejected_by_super_rh', false)
                      ->whereNull('rejected_by_current_direction')
                      ->whereNull('rejected_by_destination_direction');
                });
            })
            ->whereNotIn('id', $dismissedSuperRhMutationIds)
            ->with(['user', 'toEntite', 'approvedByCurrentDirection', 'approvedByDestinationDirection'])
            ->orderByRaw('GREATEST(COALESCE(approved_by_current_direction_at, 0), COALESCE(approved_by_destination_direction_at, 0)) DESC')
            ->get();
            
            foreach ($pendingSuperRhMutations as $mutation) {
                // Determine the stage
                $isIntermediateReview = false;
                $isFinalValidation = false;
                if ($mutation->mutation_type === 'externe') {
                    if ($mutation->approved_by_current_direction && 
                        !$mutation->sent_to_destination_by_super_rh &&
                        !$mutation->approved_by_destination_direction) {
                        $isIntermediateReview = true;
                    } elseif ($mutation->approved_by_current_direction &&
                              $mutation->sent_to_destination_by_super_rh &&
                              $mutation->approved_by_destination_direction) {
                        $isFinalValidation = true;
                    }
                } else {
                    $isFinalValidation = true;
                }
                
                $pendingSuperCollaborateurRhMutations[] = [
                    'mutation' => $mutation,
                    'to_entite_name' => $mutation->toEntite ? $mutation->toEntite->name : 'N/A',
                    'date_depot' => $mutation->created_at ? $mutation->created_at->format('d/m/Y') : 'N/A',
                    'user_name' => $mutation->user ? ($mutation->user->fname . ' ' . $mutation->user->lname) : 'N/A',
                    'is_intermediate_review' => $isIntermediateReview,
                    'is_final_validation' => $isFinalValidation,
                ];
            }
        }
            
        // Prepare additional data for the view
        $currentEntite = $currentParcours ? $currentParcours->entite : null;
        $corpsDescriptions = [
            'forestier' => 'Personnel diplômé de l\'école forestière, habilité, ou pouvant être habilité, au port d\'armes, spécialisé dans la gestion des forêts.',
            'support' => 'Personnel administratif, financier, RH ou technique, assurant un appui stratégique ou opérationnel aux services forestiers.'
        ];
        
        // Determine direction name and whether to show it
        $directionName = null;
        $shouldShowDirection = false;
        if ($userDirection) {
            $directionName = $userDirection->name;
        } elseif ($currentEntite && $currentEntite->lieu_direction) {
            $directionName = $currentEntite->lieu_direction;
        }
        
        if ($directionName && $directionName !== 'Non définie') {
            $directionNameUpper = strtoupper($directionName);
            
            // Check if it's a regional direction
            $isRegionalDirection = (
                strpos($directionNameUpper, 'DIRECTIONS REGIONALES') !== false ||
                strpos($directionNameUpper, 'DIRECTION REGIONALE') !== false
            );
            
            // Check if it's central administration (should not show)
            $isCentralAdmin = (
                strpos($directionNameUpper, 'DIRECTEUR GÉNÉRAL') !== false ||
                strpos($directionNameUpper, 'DIRECTEUR GENERAL') !== false ||
                strpos($directionNameUpper, 'SECRÉTAIRE GÉNÉRAL') !== false ||
                strpos($directionNameUpper, 'SECRETAIRE GENERAL') !== false
            );
            
            // Only show if it's a regional direction and not central admin
            if ($isRegionalDirection && !$isCentralAdmin) {
                $shouldShowDirection = true;
            }
        }
        
        // Determine ville d'affectation
        $villeAffectation = 'Non définie';
        if ($currentEntite) {
            $current = $currentEntite;
            $maxDepth = 10;
            $depth = 0;
            
            while ($current && $depth < $maxDepth) {
                // Check if this entity has lieu_affectation
                if ($current->lieu_affectation) {
                    $villeAffectation = $current->lieu_affectation;
                    break;
                }
                
                // Move to parent
                if ($current->parent_id) {
                    if (!$current->relationLoaded('parent')) {
                        $current->load('parent');
                    }
                    if ($current->parent) {
                        $current = $current->parent;
                    } else {
                        break;
                    }
                } else {
                    break;
                }
                $depth++;
            }
            
            // If still not found, try to get lieu_direction from the direction entity
            if ($villeAffectation === 'Non définie' && $userDirection && $userDirection->lieu_direction) {
                $villeAffectation = $userDirection->lieu_direction;
            }
        }
        
        // Regular user dashboard - show their personal info
        $mutationService = $this->mutationService;
        return view('hr.dashboard', compact('user', 'isChef', 'pendingDemandesForChef', 'pendingDemandesCount', 'pendingMutationsForChef', 'pendingMutationsCount', 'needsReturnDeclaration', 'returnDeclarationDemande', 'recentStatusChanges', 'recentMutationChanges', 'recentAvisRetourDeclarations', 'chefName', 'chefPpr', 'chefEntiteName', 'currentParcours', 'pendingSuperCollaborateurRhMutations', 'userDirection', 'mutationService', 'currentEntite', 'corpsDescriptions', 'directionName', 'shouldShowDirection', 'villeAffectation'));
    }

    public function getStatistics()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_demandes' => Demande::count(),
            'pending_demandes' => Demande::whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'pending');
            })->count(),
            'total_entites' => Entite::count(),
        ];

        return response()->json($stats);
    }

    /**
     * RH statistics dashboard for HR roles (admin, Collaborateur Rh, super Collaborateur Rh).
     */
    public function rhStats()
    {
        $user = auth()->user();

        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('Collaborateur Rh') &&
            !$user->hasRole('super Collaborateur Rh')
        ) {
            abort(403, 'Unauthorized.');
        }

        // Basic Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        $newUsers30d = User::where('created_at', '>=', now()->subDays(30))->count();
        $newUsers7d = User::where('created_at', '>=', now()->subDays(7))->count();

        // Leave Statistics
        $totalDemandes = Demande::where('type', 'conge')->count();
        $pendingDemandes = Demande::where('type', 'conge')
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'pending');
            })->count();
        $approvedDemandes = Demande::where('type', 'conge')
            ->where('statut', 'approved')->count();
        $rejectedDemandes = Demande::where('type', 'conge')
            ->where('statut', 'rejected')->count();
        
        // Leave requests by type
        $leavesByType = DB::table('demande_conges')
            ->join('type_conges', 'demande_conges.type_conge_id', '=', 'type_conges.id')
            ->select('type_conges.name', DB::raw('count(*) as count'))
            ->groupBy('type_conges.name')
            ->get();

        // Mutation Statistics
        $totalMutations = Mutation::count();
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

        // Entity Statistics
        $totalEntites = Entite::count();
        $entitesWithChefs = Entite::whereNotNull('chef_ppr')->count();
        $entitesWithoutChefs = $totalEntites - $entitesWithChefs;

        // Parcours/Affectation Statistics
        $totalAffectations = Parcours::whereNotNull('created_by_ppr')->count();
        $affectationsThisMonth = Parcours::whereNotNull('created_by_ppr')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Deplacement Statistics
        $totalDeplacements = Deplacement::count();
        $deplacementsThisYear = Deplacement::where('annee', now()->year)->count();
        $deplacementsThisMonth = Deplacement::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $totalDeplacementAmount = Deplacement::sum('somme') ?? 0;
        $deplacementAmountThisYear = Deplacement::where('annee', now()->year)->sum('somme') ?? 0;

        // Monthly trends (last 6 months)
        $monthlyUsers = [];
        $monthlyLeaves = [];
        $monthlyMutations = [];
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthlyUsers[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyLeaves[] = Demande::where('type', 'conge')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyMutations[] = Mutation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Users by Grade
        $usersByGrade = DB::table('user_infos')
            ->join('grades', 'user_infos.grade_id', '=', 'grades.id')
            ->select('grades.name', DB::raw('count(*) as count'))
            ->groupBy('grades.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top entities by user count (with pagination)
        $perPage = request()->get('entities_per_page', 10);
        $currentPage = request()->get('entities_page', 1);
        
        // Get total count for pagination
        $totalCount = DB::table('parcours')
            ->join('entites', 'parcours.entite_id', '=', 'entites.id')
            ->where(function($query) {
                $query->whereNull('parcours.date_fin')
                      ->orWhere('parcours.date_fin', '>=', now());
            })
            ->select('entites.id')
            ->groupBy('entites.id')
            ->get()
            ->count();
        
        // Get paginated results
        $offset = ($currentPage - 1) * $perPage;
        $topEntites = DB::table('parcours')
            ->join('entites', 'parcours.entite_id', '=', 'entites.id')
            ->where(function($query) {
                $query->whereNull('parcours.date_fin')
                      ->orWhere('parcours.date_fin', '>=', now());
            })
            ->select('entites.id', 'entites.name', DB::raw('count(distinct parcours.ppr) as count'))
            ->groupBy('entites.id', 'entites.name')
            ->orderBy('count', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
        
        // Create paginator
        $topEntites = new LengthAwarePaginator(
            $topEntites,
            $totalCount,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'entities_page',
            ]
        );
        
        // Append other query parameters
        $topEntites->appends(request()->except('entities_page'));
        
        // Prepare entity numbers for display (for pagination)
        $topEntites->getCollection()->transform(function($entite, $index) use ($topEntites) {
            $entite->entityNumber = $topEntites->firstItem() 
                ? $topEntites->firstItem() + $index 
                : $index + 1;
            return $entite;
        });

        // Recent activities (last 10)
        $recentActivities = collect();
        
        // Recent leaves
        $recentLeaves = Demande::where('type', 'conge')
            ->with(['user', 'demandeConge.typeConge'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($demande) {
                return [
                    'type' => 'leave',
                    'user' => $demande->user->fname . ' ' . $demande->user->lname,
                    'ppr' => $demande->ppr,
                    'status' => $demande->statut,
                    'date' => $demande->created_at,
                    'details' => $demande->demandeConge->typeConge->name ?? 'N/A',
                ];
            });
        
        // Recent mutations
        $recentMutations = Mutation::with(['user', 'toEntite'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($mutation) {
                return [
                    'type' => 'mutation',
                    'user' => $mutation->user->fname . ' ' . $mutation->user->lname,
                    'ppr' => $mutation->ppr,
                    'date' => $mutation->created_at,
                    'details' => $mutation->toEntite->name ?? 'N/A',
                ];
            });
        
        $recentActivities = $recentLeaves->merge($recentMutations)
            ->sortByDesc('date')
            ->take(10);

        // Calculate approval rate
        $approvalRate = $totalMutations > 0 ? round(($approvedMutations / $totalMutations) * 100, 1) : 0;

        $stats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'new_users_30d' => $newUsers30d,
            'new_users_7d' => $newUsers7d,
            'total_demandes' => $totalDemandes,
            'pending_demandes' => $pendingDemandes,
            'approved_demandes' => $approvedDemandes,
            'rejected_demandes' => $rejectedDemandes,
            'total_mutations' => $totalMutations,
            'pending_mutations' => $pendingMutations,
            'approved_mutations' => $approvedMutations,
            'approval_rate' => $approvalRate,
            'total_entites' => $totalEntites,
            'entites_with_chefs' => $entitesWithChefs,
            'entites_without_chefs' => $entitesWithoutChefs,
            'total_affectations' => $totalAffectations,
            'affectations_this_month' => $affectationsThisMonth,
            'total_deplacements' => $totalDeplacements,
            'deplacements_this_year' => $deplacementsThisYear,
            'deplacements_this_month' => $deplacementsThisMonth,
            'total_deplacement_amount' => $totalDeplacementAmount,
            'deplacement_amount_this_year' => $deplacementAmountThisYear,
            'leaves_by_type' => $leavesByType,
            'users_by_grade' => $usersByGrade,
            'top_entites' => $topEntites,
            'monthly_users' => $monthlyUsers,
            'monthly_leaves' => $monthlyLeaves,
            'monthly_mutations' => $monthlyMutations,
            'months' => $months,
            'recent_activities' => $recentActivities,
        ];

        return view('dashboard', compact('stats'));
    }

    /**
     * Check and update parcours for approved mutations that haven't been processed yet.
     */
    private function checkAndUpdateParcoursForApprovedMutations($user)
    {
        // Get approved mutations for this user that might need parcours updates
        $approvedMutations = Mutation::where('ppr', $user->ppr)
            ->where(function($query) {
                // Internal mutations: approved by current direction
                $query->where(function($q) {
                    $q->where('mutation_type', 'interne')
                      ->where('approved_by_current_direction', true)
                      ->where('rejected_by_current_direction', false);
                })
                // External mutations: approved by both directions
                ->orWhere(function($q) {
                    $q->where('mutation_type', 'externe')
                      ->where('approved_by_current_direction', true)
                      ->where('approved_by_destination_direction', true)
                      ->where('rejected_by_current_direction', false)
                      ->where('rejected_by_destination_direction', false);
                });
            })
            ->with('toEntite')
            ->get();
        
        foreach ($approvedMutations as $mutation) {
            if (!$mutation->toEntite) {
                continue;
            }
            
            // Get current active parcours
            $currentParcours = Parcours::where('ppr', $user->ppr)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->orderBy('date_debut', 'desc')
                ->first();
            
            // If no active parcours or current parcours is already for destination entity, skip
            if (!$currentParcours || $currentParcours->entite_id == $mutation->to_entite_id) {
                continue;
            }
            
            // Check if parcours was already updated for this mutation
            $approvalDate = $mutation->approved_by_current_direction_at ?? 
                           $mutation->approved_by_destination_direction_at ?? 
                           $mutation->created_at;
            $approvalDateStart = Carbon::parse($approvalDate)->startOfDay();
            
            $existingParcours = Parcours::where('ppr', $user->ppr)
                ->where('entite_id', $mutation->to_entite_id)
                ->where('date_debut', '>=', $approvalDateStart->copy()->subDays(1))
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->first();
            
            // If parcours already exists for this mutation, skip
            if ($existingParcours) {
                continue;
            }
            
            // Update parcours for this approved mutation
            $this->updateParcoursForApprovedMutation($mutation, $currentParcours);
        }
    }
    
    /**
     * Update parcours when mutation is fully approved.
     */
    private function updateParcoursForApprovedMutation($mutation, $userParcours)
    {
        if (!$mutation->toEntite) {
            return;
        }
        
        // Get approval date
        $approvalDate = $mutation->approved_by_current_direction_at ?? 
                       $mutation->approved_by_destination_direction_at ?? 
                       now();
        $approvalDateStart = Carbon::parse($approvalDate)->startOfDay();
        
        // Use transaction to ensure atomicity
        DB::transaction(function() use ($mutation, $userParcours, $approvalDateStart) {
            $today = $approvalDateStart->copy();
            
            // Close the current parcours by setting date_fin to the day before new one starts
            $endDate = $today->copy()->subDay();
            
            // Only update if the parcours is still active (date_fin is null or in the future)
            if (is_null($userParcours->date_fin) || $userParcours->date_fin >= now()) {
                $userParcours->date_fin = $endDate;
                $userParcours->save();
            }

            // Use updateOrCreate to handle existing parcours safely
            Parcours::updateOrCreate(
                [
                    'ppr' => $mutation->ppr,
                    'entite_id' => $mutation->to_entite_id,
                ],
                [
                    'poste' => $userParcours->poste ?? 'Agent', // Keep same poste or default
                    'date_debut' => $today,
                    'date_fin' => null, // Active parcours
                    'grade_id' => $userParcours->grade_id, // Preserve grade
                    'reason' => $mutation->motif ?? 'Mutation approuvée', // Use mutation motif as reason
                ]
            );
        });
    }

    /**
     * Dismiss an alert for the current user
     */
    public function dismissAlert(Request $request, $demandeId)
    {
        $user = auth()->user();
        
        // Verify the demande belongs to the user
        $demande = Demande::where('id', $demandeId)
            ->where('ppr', $user->ppr)
            ->firstOrFail();
        
        // Create or update dismissed alert record
        DismissedAlert::firstOrCreate([
            'ppr' => $user->ppr,
            'demande_id' => $demandeId,
            'alert_type' => 'status_change',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed successfully',
        ]);
    }

    /**
     * Dismiss a mutation alert for the current user
     */
    public function dismissMutationAlert(Request $request, $mutationId)
    {
        $user = auth()->user();
        
        // Verify the mutation belongs to the user
        $mutation = Mutation::where('id', $mutationId)
            ->where('ppr', $user->ppr)
            ->firstOrFail();
        
        // Create or update dismissed alert record (reusing demande_id column for mutation_id)
        DismissedAlert::firstOrCreate([
            'ppr' => $user->ppr,
            'demande_id' => $mutationId, // Reusing this column for mutation_id
            'alert_type' => 'mutation_status_change',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed successfully',
        ]);
    }

    /**
     * Dismiss a super Collaborateur Rh mutation alert
     */
public function dismissSuperRhMutationAlert(Request $request, $mutationId)
    {
        $user = auth()->user();
        
        // Verify user has super Collaborateur Rh role
        if (!$user->hasRole('super Collaborateur Rh')) {
            abort(403, 'Unauthorized. You must be a super Collaborateur Rh to dismiss this alert.');
        }
        
        // Verify the mutation exists and is pending super Collaborateur Rh validation
        $mutation = Mutation::where('id', $mutationId)
            ->where('approved_by_super_collaborateur_rh', false)
            ->firstOrFail();
        
        // Create or update dismissed alert record
        DismissedAlert::firstOrCreate([
            'ppr' => $user->ppr,
            'demande_id' => $mutationId, // Reusing this column for mutation_id
            'alert_type' => 'mutation_pending_super_rh',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed successfully',
        ]);
    }

    /**
     * Dismiss an avis de retour declaration alert for the chef
     */
    public function dismissAvisRetourAlert(Request $request, $demandeId)
    {
        $user = auth()->user();
        
        // Verify user is a chef
        if (!$user->isChef()) {
            abort(403, 'Unauthorized. You must be a chef to dismiss this alert.');
        }
        
        // Verify the demande exists and belongs to one of the chef's collaborators
        $chefEntiteIds = Entite::where('chef_ppr', $user->ppr)
            ->pluck('id')
            ->toArray();
        
        // Get all descendant entities
        $allEntiteIds = $chefEntiteIds;
        foreach ($chefEntiteIds as $entiteId) {
            $entite = Entite::find($entiteId);
            if ($entite) {
                $descendants = $this->getDescendantEntiteIds($entite);
                $allEntiteIds = array_merge($allEntiteIds, $descendants);
            }
        }
        $allEntiteIds = array_unique($allEntiteIds);
        
        // Get PPRs of users who are CURRENTLY in these entities (active parcours only)
        $userPprs = Parcours::whereIn('entite_id', $allEntiteIds)
            ->where('ppr', '!=', $user->ppr)
            ->where(function($query) {
                // Only active parcours: no end date OR end date is in the future
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->where(function($query) {
                // Ensure date_debut is not in the future (parcours has started)
                $query->whereNull('date_debut')
                      ->orWhere('date_debut', '<=', now());
            })
            ->orderBy('date_debut', 'desc') // Get most recent parcours first
            ->get()
            ->unique('ppr') // Only keep one parcours per user (most recent)
            ->pluck('ppr')
            ->toArray();
        
        $demande = Demande::where('id', $demandeId)
            ->whereIn('ppr', $userPprs)
            ->firstOrFail();
        
        // Create or update dismissed alert record
        DismissedAlert::firstOrCreate([
            'ppr' => $user->ppr,
            'demande_id' => $demandeId,
            'alert_type' => 'avis_retour_declaration',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed successfully',
        ]);
    }

    /**
     * Recursively get all descendant entity IDs.
     */
    private function getDescendantEntiteIds(Entite $entite, array &$ids = []): array
    {
        $children = Entite::where('parent_id', $entite->id)->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            $this->getDescendantEntiteIds($child, $ids);
        }
        return $ids;
    }
}

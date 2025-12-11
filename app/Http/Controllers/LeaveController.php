<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Conge;
use App\Models\Avis;
use App\Models\AvisDepart;
use App\Models\AvisRetour;
use App\Models\Entite;
use App\Models\Parcours;
use App\Models\User;
use App\Models\JoursFerie;
use App\Models\DemandeConge;
use App\Models\CongeMaladie;
use App\Models\TypeMaladie;
use App\Models\TypeConge;
use App\Services\NotificationService;
use App\Services\CongeService;
use App\Services\LeavePDFService;
use App\Mail\LeaveRequestNotification;
use App\Actions\AvisDepart\ValidateAvisDepartAction;
use App\Actions\AvisDepart\RejectAvisDepartAction;
use App\Actions\AvisRetour\ValidateAvisRetourAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DomainException;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveController extends Controller
{
    protected LeavePDFService $pdfService;

    public function __construct(LeavePDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $demandes = Demande::with('user')->paginate(20);
        return view('leaves.index', compact('demandes'));
    }

    /**
     * Show annual administrative leave details
     */
    public function annuel()
    {
        $user = auth()->user();
        
        // Calculate leave balance using the action
        $leaveData = app(\App\Actions\Conge\CalculateCongeBalanceAction::class)->execute($user);
        
        // Extract variables for the view
        $hasRemainingLeave = $leaveData['has_remaining_leave'] ?? false;
        $hasPendingDemande = $leaveData['has_pending_demande'] ?? false;
        $hasDemandeWithoutRetour = $leaveData['has_demande_without_retour'] ?? false;
        $hasApprovedDemandeWithoutRetour = $leaveData['has_approved_demande_without_retour'] ?? false;
        $hasPendingAvisDepart = $leaveData['has_pending_avis_depart'] ?? false;
        $hasPendingAvisRetour = $leaveData['has_pending_avis_retour'] ?? false;
        $hasReturnDateToday = $leaveData['demandes_with_return_today'] && $leaveData['demandes_with_return_today']->count() > 0;
        
        // Get pending demande if exists
        $pendingDemande = null;
        if ($hasPendingDemande) {
            $pendingDemande = Demande::where('ppr', $user->ppr)
                ->whereHas('avis.avisDepart', function($query) {
                    $query->where('statut', 'pending');
                })
                ->with(['avis.avisDepart'])
                ->first();
        }
        
        return view('leaves.annuel', compact(
            'user',
            'leaveData',
            'hasRemainingLeave',
            'hasPendingDemande',
            'hasDemandeWithoutRetour',
            'hasApprovedDemandeWithoutRetour',
            'hasPendingAvisDepart',
            'hasPendingAvisRetour',
            'hasReturnDateToday',
            'pendingDemande'
        ));
    }

    /**
     * Show list of maladie leave requests
     */
    public function indexMaladie(Request $request)
    {
        $user = auth()->user();
        
        $filters = [
            'year' => $request->get('year', date('Y')),
            'status' => $request->get('status', ''),
            'search' => $request->get('search', ''),
        ];
        
        $perPage = $request->get('per_page', 15);
        $demandes = app(\App\Actions\Conge\ListMaladieAction::class)->execute($user, $filters, $perPage);
        
        return view('leaves.index-maladie', compact('demandes', 'filters', 'perPage'));
    }

    /**
     * Show demandes from chef's agents
     */
    public function agentsDemandes(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a chef
        $isChef = Entite::where('chef_ppr', $user->ppr)->exists();
        
        if (!$isChef && !$user->hasRole('admin')) {
            abort(403, 'Seuls les chefs d\'entité peuvent accéder à cette page.');
        }
        
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
        // This ensures we only show demandes from current agents, not past ones
        $agentPprs = Parcours::whereIn('entite_id', $allEntiteIds)
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
        
        // Build query for demandes
        $query = Demande::with(['avis.avisDepart', 'avis.avisRetour', 'user', 'demandeConge.typeConge']);
        
        // Filter by agent PPRs
        if ($user->hasRole('admin')) {
            // Admin can see all demandes
        } else {
            $query->whereIn('ppr', $agentPprs);
        }
        
        // Filter by status
        $statut = $request->get('statut');
        if ($statut) {
            $query->whereHas('avis.avisDepart', function($q) use ($statut) {
                $q->where('statut', $statut);
            });
        }
        
        // Filter by year
        $year = $request->get('year', date('Y'));
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        // Filter by month
        $month = $request->get('month');
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        
        // Get demandes with pagination
        $perPage = $request->get('per_page', 15);
        $demandes = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Transform demandes for the view
        $transformedItems = $demandes->getCollection()->map(function($demande) {
            $avis = $demande->avis;
            $avisDepart = $avis ? $avis->avisDepart : null;
            $avisRetour = $avis ? $avis->avisRetour : null;
            
            // Get type demande
            $typeDemande = 'Congé Administratif Annuel';
            if ($demande->demandeConge && $demande->demandeConge->typeConge) {
                $typeDemande = $demande->demandeConge->typeConge->name;
            }
            
            // Get statut from avis de départ
            $statut = 'pending';
            if ($avisDepart) {
                $statut = $avisDepart->statut;
            }
            
            // Get statut labels
            $avisDepartStatutLabel = match($statut) {
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default => 'En attente',
            };
            
            $avisRetourStatut = $avisRetour ? $avisRetour->statut : null;
            $avisRetourStatutLabel = match($avisRetourStatut) {
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default => null,
            };
            
            // Create object-like structure for the view
            return (object) [
                'id' => $demande->id,
                'ppr' => $demande->ppr,
                'user_name' => $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A',
                'type_demande' => $typeDemande,
                'date_depot' => $demande->created_at,
                'statut' => $statut,
                'nbr_jours' => $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0,
                'date_debut' => $avisDepart ? $avisDepart->date_depart : ($demande->date_debut ?? null),
                'date_depart' => $avisDepart ? $avisDepart->date_depart : null,
                'date_retour' => $avisDepart ? $avisDepart->date_retour : null,
                'avis_depart_statut' => $statut,
                'avis_depart_statut_label' => $avisDepartStatutLabel,
                'avis_depart_id' => $avisDepart ? $avisDepart->id : null,
                'avis_depart_pdf_path' => $avisDepart ? $avisDepart->pdf_path : null,
                'nbr_jours_consommes' => $avisRetour ? ($avisRetour->nbr_jours_consumes ?? 0) : 0,
                'date_retour_declaree' => $avisRetour ? $avisRetour->date_retour_declaree : null,
                'date_retour_effectif' => $avisRetour ? $avisRetour->date_retour_effectif : null,
                'avis_retour_statut' => $avisRetourStatut,
                'avis_retour_statut_label' => $avisRetourStatutLabel,
                'avis_retour_id' => $avisRetour ? $avisRetour->id : null,
                'avis_retour_pdf_path' => $avisRetour ? $avisRetour->pdf_path : null,
                'avis_retour_date_depot' => $avisRetour ? ($avisRetour->created_at ?? ($avis ? $avis->date_depot : null)) : null,
                'explanation_pdf_path' => $avisRetour ? $avisRetour->explanation_pdf_path : null,
                'consumption_exceeds' => $avisRetour && $avisRetour->date_retour_declaree && $avisRetour->date_retour_effectif 
                    ? Carbon::parse($avisRetour->date_retour_effectif)->greaterThan(Carbon::parse($avisRetour->date_retour_declaree))
                    : false,
                'avis_depart' => $avisDepart ? (object) [
                    'id' => $avisDepart->id,
                    'statut' => $avisDepart->statut,
                    'date_depart' => $avisDepart->date_depart,
                    'date_retour' => $avisDepart->date_retour,
                    'nb_jours_demandes' => $avisDepart->nb_jours_demandes ?? 0,
                    'pdf_path' => $avisDepart->pdf_path ?? null,
                ] : null,
                'avis_retour' => $avisRetour ? (object) [
                    'id' => $avisRetour->id,
                    'statut' => $avisRetour->statut,
                    'nbr_jours_consommes' => $avisRetour->nbr_jours_consumes ?? 0,
                    'date_retour_declaree' => $avisRetour->date_retour_declaree,
                    'date_retour_effectif' => $avisRetour->date_retour_effectif,
                    'pdf_path' => $avisRetour->pdf_path ?? null,
                    'explanation_pdf_path' => $avisRetour->explanation_pdf_path ?? null,
                ] : null,
            ];
        });
        
        // Replace the collection with transformed items
        $demandes->setCollection($transformedItems);
        
        return view('leaves.agents', compact('demandes'));
    }

    /**
     * Show demandes from users in central entities (Admin only)
     */
    public function centralDemandes(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin')) {
            abort(403, 'Seuls les administrateurs peuvent accéder à cette page.');
        }

        // Get all central entities
        $centralEntiteIds = Entite::whereHas('entiteInfo', function($query) {
                $query->where('type', 'central');
            })
            ->pluck('id');

        // Get PPRs of users in central entities
        $userPprs = Parcours::whereIn('entite_id', $centralEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->pluck('ppr')
            ->unique()
            ->toArray();

        // Build query for demandes
        $query = Demande::with(['avis.avisDepart', 'avis.avisRetour', 'user', 'demandeConge.typeConge'])
            ->whereIn('ppr', $userPprs);

        // Filter by status
        $statut = $request->get('statut');
        if ($statut) {
            $query->whereHas('avis.avisDepart', function($q) use ($statut) {
                $q->where('statut', $statut);
            });
        }

        // Filter by year
        $year = $request->get('year', date('Y'));
        if ($year) {
            $query->whereYear('created_at', $year);
        }

        // Filter by month
        $month = $request->get('month');
        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        // Get demandes with pagination
        $perPage = $request->get('per_page', 15);
        $demandes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform demandes for the view (same as agentsDemandes)
        $transformedItems = $demandes->getCollection()->map(function($demande) {
            $avis = $demande->avis;
            $avisDepart = $avis ? $avis->avisDepart : null;
            $avisRetour = $avis ? $avis->avisRetour : null;
            
            // Get type demande
            $typeDemande = 'Congé Administratif Annuel';
            if ($demande->demandeConge && $demande->demandeConge->typeConge) {
                $typeDemande = $demande->demandeConge->typeConge->name;
            }
            
            // Get statut from avis de départ
            $statut = 'pending';
            if ($avisDepart) {
                $statut = $avisDepart->statut;
            }
            
            // Get statut labels
            $avisDepartStatutLabel = match($statut) {
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default => 'En attente',
            };
            
            $avisRetourStatut = $avisRetour ? $avisRetour->statut : null;
            $avisRetourStatutLabel = match($avisRetourStatut) {
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default => null,
            };
            
            // Create object-like structure for the view
            return (object) [
                'id' => $demande->id,
                'ppr' => $demande->ppr,
                'user_name' => $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A',
                'type_demande' => $typeDemande,
                'date_depot' => $demande->created_at,
                'statut' => $statut,
                'nbr_jours' => $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0,
                'date_debut' => $avisDepart ? $avisDepart->date_depart : ($demande->date_debut ?? null),
                'date_depart' => $avisDepart ? $avisDepart->date_depart : null,
                'date_retour' => $avisDepart ? $avisDepart->date_retour : null,
                'avis_depart_statut' => $statut,
                'avis_depart_statut_label' => $avisDepartStatutLabel,
                'avis_depart_id' => $avisDepart ? $avisDepart->id : null,
                'avis_depart_pdf_path' => $avisDepart ? $avisDepart->pdf_path : null,
                'nbr_jours_consommes' => $avisRetour ? ($avisRetour->nbr_jours_consumes ?? 0) : 0,
                'date_retour_declaree' => $avisRetour ? $avisRetour->date_retour_declaree : null,
                'date_retour_effectif' => $avisRetour ? $avisRetour->date_retour_effectif : null,
                'avis_retour_statut' => $avisRetourStatut,
                'avis_retour_statut_label' => $avisRetourStatutLabel,
                'avis_retour_id' => $avisRetour ? $avisRetour->id : null,
                'avis_retour_pdf_path' => $avisRetour ? $avisRetour->pdf_path : null,
                'avis_retour_date_depot' => $avisRetour ? ($avisRetour->created_at ?? ($avis ? $avis->date_depot : null)) : null,
                'explanation_pdf_path' => $avisRetour ? $avisRetour->explanation_pdf_path : null,
                'consumption_exceeds' => false,
                'avis_depart' => $avisDepart ? (object) [
                    'id' => $avisDepart->id,
                    'statut' => $avisDepart->statut,
                    'date_depart' => $avisDepart->date_depart,
                    'date_retour' => $avisDepart->date_retour,
                    'nb_jours_demandes' => $avisDepart->nb_jours_demandes ?? 0,
                    'pdf_path' => $avisDepart->pdf_path ?? null,
                ] : null,
                'avis_retour' => $avisRetour ? (object) [
                    'id' => $avisRetour->id,
                    'statut' => $avisRetour->statut,
                    'nbr_jours_consommes' => $avisRetour->nbr_jours_consumes ?? 0,
                    'date_retour_declaree' => $avisRetour->date_retour_declaree,
                    'date_retour_effectif' => $avisRetour->date_retour_effectif,
                    'pdf_path' => $avisRetour->pdf_path ?? null,
                    'explanation_pdf_path' => $avisRetour->explanation_pdf_path ?? null,
                ] : null,
            ];
        });

        // Replace the collection with transformed items
        $demandes->setCollection($transformedItems);
        
        return view('leaves.central', compact('demandes', 'statut', 'year', 'month', 'perPage'));
    }

    /**
     * Show demandes from users in regional entities (Admin only)
     */
    public function regionalDemandes(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin')) {
            abort(403, 'Seuls les administrateurs peuvent accéder à cette page.');
        }

        // Get all regional entities
        $regionalEntiteIds = Entite::whereHas('entiteInfo', function($query) {
                $query->where('type', 'regional');
            })
            ->pluck('id');

        // Get PPRs of users in regional entities
        $userPprs = Parcours::whereIn('entite_id', $regionalEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->pluck('ppr')
            ->unique()
            ->toArray();

        // Build query for demandes
        $query = Demande::with(['avis.avisDepart', 'avis.avisRetour', 'user', 'demandeConge.typeConge'])
            ->whereIn('ppr', $userPprs);

        // Filter by status
        $statut = $request->get('statut');
        if ($statut) {
            $query->whereHas('avis.avisDepart', function($q) use ($statut) {
                $q->where('statut', $statut);
            });
        }

        // Filter by year
        $year = $request->get('year', date('Y'));
        if ($year) {
            $query->whereYear('created_at', $year);
        }

        // Filter by month
        $month = $request->get('month');
        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        // Get demandes with pagination
        $perPage = $request->get('per_page', 15);
        $demandes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform demandes for the view (same as agentsDemandes)
        $transformedItems = $demandes->getCollection()->map(function($demande) {
            $avis = $demande->avis;
            $avisDepart = $avis ? $avis->avisDepart : null;
            $avisRetour = $avis ? $avis->avisRetour : null;
            
            // Get type demande
            $typeDemande = 'Congé Administratif Annuel';
            if ($demande->demandeConge && $demande->demandeConge->typeConge) {
                $typeDemande = $demande->demandeConge->typeConge->name;
            }
            
            // Get statut from avis de départ
            $statut = 'pending';
            if ($avisDepart) {
                $statut = $avisDepart->statut;
            }
            
            // Get statut labels
            $avisDepartStatutLabel = match($statut) {
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default => 'En attente',
            };
            
            $avisRetourStatut = $avisRetour ? $avisRetour->statut : null;
            $avisRetourStatutLabel = match($avisRetourStatut) {
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
                default => null,
            };
            
            // Create object-like structure for the view
            return (object) [
                'id' => $demande->id,
                'ppr' => $demande->ppr,
                'user_name' => $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A',
                'type_demande' => $typeDemande,
                'date_depot' => $demande->created_at,
                'statut' => $statut,
                'nbr_jours' => $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0,
                'date_debut' => $avisDepart ? $avisDepart->date_depart : ($demande->date_debut ?? null),
                'date_depart' => $avisDepart ? $avisDepart->date_depart : null,
                'date_retour' => $avisDepart ? $avisDepart->date_retour : null,
                'avis_depart_statut' => $statut,
                'avis_depart_statut_label' => $avisDepartStatutLabel,
                'avis_depart_id' => $avisDepart ? $avisDepart->id : null,
                'avis_depart_pdf_path' => $avisDepart ? $avisDepart->pdf_path : null,
                'nbr_jours_consommes' => $avisRetour ? ($avisRetour->nbr_jours_consumes ?? 0) : 0,
                'date_retour_declaree' => $avisRetour ? $avisRetour->date_retour_declaree : null,
                'date_retour_effectif' => $avisRetour ? $avisRetour->date_retour_effectif : null,
                'avis_retour_statut' => $avisRetourStatut,
                'avis_retour_statut_label' => $avisRetourStatutLabel,
                'avis_retour_id' => $avisRetour ? $avisRetour->id : null,
                'avis_retour_pdf_path' => $avisRetour ? $avisRetour->pdf_path : null,
                'avis_retour_date_depot' => $avisRetour ? ($avisRetour->created_at ?? ($avis ? $avis->date_depot : null)) : null,
                'explanation_pdf_path' => $avisRetour ? $avisRetour->explanation_pdf_path : null,
                'consumption_exceeds' => false,
                'avis_depart' => $avisDepart ? (object) [
                    'id' => $avisDepart->id,
                    'statut' => $avisDepart->statut,
                    'date_depart' => $avisDepart->date_depart,
                    'date_retour' => $avisDepart->date_retour,
                    'nb_jours_demandes' => $avisDepart->nb_jours_demandes ?? 0,
                    'pdf_path' => $avisDepart->pdf_path ?? null,
                ] : null,
                'avis_retour' => $avisRetour ? (object) [
                    'id' => $avisRetour->id,
                    'statut' => $avisRetour->statut,
                    'nbr_jours_consommes' => $avisRetour->nbr_jours_consumes ?? 0,
                    'date_retour_declaree' => $avisRetour->date_retour_declaree,
                    'date_retour_effectif' => $avisRetour->date_retour_effectif,
                    'pdf_path' => $avisRetour->pdf_path ?? null,
                    'explanation_pdf_path' => $avisRetour->explanation_pdf_path ?? null,
                ] : null,
            ];
        });

        // Replace the collection with transformed items
        $demandes->setCollection($transformedItems);
        
        return view('leaves.regional', compact('demandes', 'statut', 'year', 'month', 'perPage'));
    }

    /**
     * Show current leave balance (solde actuel) of chef's agents
     */
    public function agentsSolde(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a chef
        $isChef = Entite::where('chef_ppr', $user->ppr)->exists();
        
        if (!$isChef && !$user->hasRole('admin')) {
            abort(403, 'Seuls les chefs d\'entité peuvent accéder à cette page.');
        }
        
        // Get entities where user is chef
        $chefEntiteIds = Entite::where('chef_ppr', $user->ppr)
            ->pluck('id');
        
        // Get PPRs of users in these entities (excluding the chef)
        $agentPprs = Parcours::whereIn('entite_id', $chefEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->where('ppr', '!=', $user->ppr)
            ->pluck('ppr')
            ->unique()
            ->toArray();
        
        // If admin, show all users
        if ($user->hasRole('admin')) {
            $agents = User::with(['userInfo', 'parcours.entite', 'parcours.grade'])->get();
        } else {
            $agents = User::whereIn('ppr', $agentPprs)
                ->with(['userInfo', 'parcours.entite', 'parcours.grade'])
                ->get();
        }
        
        // Get current year
        $currentYear = $request->input('year', Carbon::now()->year);
        
        // Calculate leave balance for each agent
        $congeService = app(CongeService::class);
        $agentsWithBalance = [];
        
        foreach ($agents as $agent) {
            $balance = $congeService->calculateAnnualBalance($agent->ppr, $currentYear);
            $agentsWithBalance[] = [
                'user' => $agent,
                'balance' => $balance,
            ];
        }
        
        // Sort by remaining days (descending)
        usort($agentsWithBalance, function($a, $b) {
            return $b['balance']['reste'] <=> $a['balance']['reste'];
        });
        
        return view('leaves.agents-solde', compact('agentsWithBalance', 'currentYear'));
    }

    /**
     * Controle central
     */
    public function controleCentral(Request $request)
    {
        // Implementation needed - placeholder
        return view('leaves.controle-central');
    }

    /**
     * Controle regional
     */
    public function controleRegional(Request $request)
    {
        // Implementation needed - placeholder
        return view('leaves.controle-regional');
    }

    /**
     * Show form for creating leave request
     */
    public function create()
    {
        // Implementation needed - placeholder
        return view('leaves.create');
    }

    /**
     * Show form for creating sick leave request
     */
    public function createMaladie()
    {
        $user = auth()->user();
        
        $data = app(\App\Actions\Conge\PrepareCreateMaladieFormAction::class)->execute($user);
        
        return view('leaves.create-maladie', $data);
    }

    /**
     * Store sick leave request (bypasses chef approval)
     */
    public function storeMaladie(\App\Http\Requests\StoreMaladieRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $dto = new \App\DTOs\Conge\CreateMaladieDTO(
            ppr: $user->ppr,
            typeMaladieId: $validated['type_maladie_id'],
            dateDeclaration: $validated['date_declaration'],
            dateConstatation: $validated['date_constatation'] ?? null,
            dateDepart: $validated['date_depart'],
            dateRetour: $validated['date_retour'] ?? null,
            nbrJoursDemandes: $validated['nbr_jours_demandes'],
            referenceArret: $validated['reference_arret'] ?? null,
            observation: $validated['observation'] ?? null
        );

        try {
            $result = app(\App\Actions\Conge\CreateMaladieAction::class)->execute($dto);
            
            return redirect()->route('hr.leaves.index')
                ->with('success', "Votre demande de {$result['typeMaladie']->display_name} a été enregistrée avec succès. Avis de départ approuvé automatiquement.");
        } catch (DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show form for creating maternity leave request
     */
    public function createMaternite()
    {
        $user = auth()->user();
        
        // Get user's current entity
        $parcours = Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with('entite')
            ->first();
        
        $entite = $parcours ? $parcours->entite : null;
        
        return view('leaves.create-maternite', compact('entite'));
    }

    /**
     * Store maternity leave request (bypasses chef approval)
     */
    public function storeMaternite(\App\Http\Requests\StoreMaterniteRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $dto = new \App\DTOs\Conge\CreateMaterniteDTO(
            ppr: $user->ppr,
            dateDeclaration: $validated['date_declaration'],
            dateDepart: $validated['date_depart'],
            dateRetour: $validated['date_retour'] ?? null,
            nbrJoursDemandes: $validated['nbr_jours_demandes'],
            observation: $validated['observation'] ?? null
        );

        try {
            $result = app(\App\Actions\Conge\CreateMaterniteAction::class)->execute($dto);
            
            return redirect()->route('hr.leaves.index')
                ->with('success', "Votre demande de {$result['typeMaladie']->display_name} a été enregistrée avec succès. Avis de départ approuvé automatiquement.");
        } catch (DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get holidays
     */
    public function getHolidays(Request $request)
    {
        // Implementation needed - placeholder
        return response()->json([]);
    }

    /**
     * Show declare retour form
     */
    public function showDeclareRetour()
    {
        $user = Auth::user();
        
        // Get all approved demandes for the user (with or without avis retour)
        // - User's own demandes
        // - Avis de départ is approved
        $demandes = Demande::where('ppr', $user->ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'approved');
            })
            ->with(['avis.avisDepart', 'avis.avisRetour', 'avis'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get holidays for the calendar
        $holidays = JoursFerie::select('date', 'name')
            ->whereYear('date', '>=', date('Y') - 1)
            ->whereYear('date', '<=', date('Y') + 1)
            ->get()
            ->map(function($holiday) {
                return [
                    'date' => Carbon::parse($holiday->date)->format('Y-m-d'),
                    'name' => $holiday->name
                ];
            });
        
        return view('leaves.declare-retour', compact('demandes', 'holidays'));
    }

    /**
     * Store declare retour
     */
    public function storeDeclareRetour(Request $request)
    {
        $request->validate([
            'demande_id' => 'required|exists:demandes,id',
            'avis_id' => 'required|exists:avis,id',
            'date_retour_declaree' => 'required|date',
            'date_retour_effectif' => 'nullable|date',
            'nbr_jours_consumes' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        
        // Verify that the demande belongs to the user
        $demande = Demande::findOrFail($request->demande_id);
        if ($demande->ppr !== $user->ppr) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à déclarer un retour pour cette demande.');
        }
        
        // Verify that avis de départ is approved
        $avis = $demande->avis;
        if (!$avis || !$avis->avisDepart || $avis->avisDepart->statut !== 'approved') {
            return redirect()->back()->with('error', 'L\'avis de départ doit être approuvé avant de pouvoir déclarer un retour.');
        }
        
        // Check if avis retour already exists
        if ($avis->avisRetour) {
            return redirect()->back()->with('error', 'Un avis de retour existe déjà pour cette demande.');
        }
        
        // Calculate nbr_jours_consumes if not provided
        $nbrJoursConsumes = $request->nbr_jours_consumes;
        if (!$nbrJoursConsumes && $avis->avisDepart->date_depart && $request->date_retour_declaree) {
            $dateDepart = Carbon::parse($avis->avisDepart->date_depart);
            $dateRetour = Carbon::parse($request->date_retour_declaree);
            
            // If same day, 0 days consumed
            if ($dateDepart->isSameDay($dateRetour)) {
                $nbrJoursConsumes = 0;
            } else {
                // Calculate difference: if return is after departure, count the days
                if ($dateRetour->greaterThan($dateDepart)) {
                    $nbrJoursConsumes = $dateDepart->diffInDays($dateRetour) + 1; // +1 to include both days
                } else {
                    $nbrJoursConsumes = 0; // Return before departure shouldn't happen, but set to 0
                }
            }
        }
        
        // Create avis retour
        $avisRetour = AvisRetour::create([
            'avis_id' => $request->avis_id,
            'date_retour_declaree' => $request->date_retour_declaree,
            'date_retour_effectif' => $request->date_retour_effectif ?? $request->date_retour_declaree,
            'nbr_jours_consumes' => $nbrJoursConsumes ?? 0,
            'statut' => 'pending',
        ]);
        
        // Notify the chef about the avis de retour declaration
        $this->notifyChefAboutAvisRetour($demande, $avisRetour);
        
        return redirect()->route('leaves.tracking')->with('success', 'Avis de retour déclaré avec succès.');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ppr' => 'required|string',
            'date_depart' => 'required|date',
            'date_retour' => 'required|date|after:date_depart',
            'nb_jours_demandes' => 'required|integer|min:1|max:22',
        ]);

        $user = Auth::user();
        
        // Get user's current entity
        $parcours = Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->first();
        
        $entiteId = $parcours ? $parcours->entite_id : null;

        // Get or create type_conge for 'annuel'
        $typeConge = TypeConge::firstOrCreate(
            ['name' => 'annuel'],
            ['name' => 'annuel']
        );

        try {
            // Create Demande (pending approval for annual leave)
            $demande = Demande::create([
                'ppr' => $user->ppr,
                'type' => 'conge',
                'entite_id' => $entiteId,
                'created_by' => $user->ppr,
                'date_debut' => $request->date_depart,
                'statut' => 'pending', // Needs chef approval
            ]);

            // Create DemandeConge
            $demandeConge = DemandeConge::create([
                'demande_id' => $demande->id,
                'type_conge_id' => $typeConge->id,
                'date_debut' => $request->date_depart,
                'date_fin' => $request->date_retour,
                'nbr_jours_demandes' => $request->nb_jours_demandes,
                'motif' => null,
            ]);

            // Create Avis (needs validation)
            $avis = Avis::create([
                'demande_id' => $demande->id,
                'date_depot' => Carbon::now(),
                'is_validated' => false, // Needs chef validation
            ]);

            // Create AvisDepart (pending approval)
            $avisDepart = AvisDepart::create([
                'avis_id' => $avis->id,
                'nb_jours_demandes' => $request->nb_jours_demandes,
                'date_depart' => $request->date_depart,
                'date_retour' => $request->date_retour,
                'statut' => 'pending', // Needs chef approval
            ]);

            // Notify the chef about the new leave request
            $this->notifyChefAboutLeaveRequest($demande, $avisDepart, $parcours);

            return redirect()->route('leaves.tracking')
                ->with('success', 'Votre demande de congé a été enregistrée avec succès. Elle est en attente de validation par votre chef.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de votre demande. Veuillez réessayer.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Demande $demande)
    {
        // Implementation needed - placeholder
        return view('leaves.show', compact('demande'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Demande $demande)
    {
        // Implementation needed - placeholder
        return view('leaves.edit', compact('demande'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Demande $demande)
    {
        // Implementation needed - placeholder
        return redirect()->route('hr.leaves.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Demande $demande)
    {
        $user = Auth::user();
        
        // Verify that the demande belongs to the user
        if ($demande->ppr !== $user->ppr) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer cette demande.');
        }
        
        // Check if avis de départ exists and is still pending
        // Users can delete their demande as long as it's pending, regardless of departure date
        $avis = $demande->avis;
        if ($avis && $avis->avisDepart) {
            $avisDepart = $avis->avisDepart;
            if ($avisDepart->statut !== 'pending') {
                return redirect()->back()->with('error', 'Vous ne pouvez supprimer une demande que si elle est encore en attente d\'approbation.');
            }
        }
        
        // Also check if avis de retour exists - if it's approved, can't delete
        if ($avis && $avis->avisRetour) {
            $avisRetour = $avis->avisRetour;
            if ($avisRetour->statut === 'approved') {
                return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer une demande dont l\'avis de retour a été approuvé.');
            }
        }
        
        // Delete the demande (cascade will handle related records: avis, avisDepart, avisRetour, etc.)
        $demande->delete();
        
        return redirect()->route('leaves.tracking')->with('success', 'Demande supprimée avec succès.');
    }

    /**
     * Approve demande
     */
    public function approve(Request $request, Demande $demande)
    {
        // Implementation needed - placeholder
        return redirect()->back();
    }

    /**
     * Reject demande
     */
    public function reject(Request $request, Demande $demande)
    {
        // Implementation needed - placeholder
        return redirect()->back();
    }

    /**
     * Approve as chef
     */
    public function approveAsChef(Request $request, Demande $demande)
    {
        // Implementation needed - placeholder
        return redirect()->back();
    }

    /**
     * Reject as chef
     */
    public function rejectAsChef(Request $request, Demande $demande)
    {
        // Implementation needed - placeholder
        return redirect()->back();
    }

    /**
     * Validate avis depart
     */
    public function validateAvisDepart(Request $request, AvisDepart $avisDepart)
    {
        try {
            $user = Auth::user();
            $demande = $avisDepart->avis->demande;
            $demandeUser = $demande->user;
            
            // Check if user is chef of demande user
            $isChefOfUser = function($validator, $demandeUser) {
                $chefEntiteIds = Entite::where('chef_ppr', $validator->ppr)->pluck('id');
                if ($chefEntiteIds->isEmpty()) {
                    return false;
                }
                
                $userParcours = Parcours::where('ppr', $demandeUser->ppr)
                    ->whereIn('entite_id', $chefEntiteIds)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->exists();
                
                return $userParcours;
            };
            
            // Generate PDF callback using service
            $generatePdfCallback = function($avisDepart, $user) {
                return $this->pdfService->generateAvisDepartPDF($avisDepart, $user);
            };
            
            $validateAction = app(ValidateAvisDepartAction::class);
            $validateAction->execute($avisDepart, $user, $isChefOfUser, $generatePdfCallback);
            
            return redirect()->back()->with('success', 'Avis de départ approuvé avec succès.');
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Error validating avis de départ: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'approbation de l\'avis de départ: ' . $e->getMessage());
        }
    }

    /**
     * Reject avis depart
     */
    public function rejectAvisDepart(Request $request, AvisDepart $avisDepart)
    {
        try {
            $user = Auth::user();
            $demande = $avisDepart->avis->demande;
            $demandeUser = $demande->user;
            
            // Check if user is chef of demande user
            $isChefOfUser = function($validator, $demandeUser) {
                $chefEntiteIds = Entite::where('chef_ppr', $validator->ppr)->pluck('id');
                if ($chefEntiteIds->isEmpty()) {
                    return false;
                }
                
                $userParcours = Parcours::where('ppr', $demandeUser->ppr)
                    ->whereIn('entite_id', $chefEntiteIds)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->exists();
                
                return $userParcours;
            };
            
            $rejectAction = app(RejectAvisDepartAction::class);
            $rejectAction->execute($avisDepart, $user, $isChefOfUser);
            
            $message = 'Avis de départ rejeté avec succès.';
            if ($request->has('rejection_reason') && $request->rejection_reason) {
                $message .= ' Raison: ' . $request->rejection_reason;
            }
            
            return redirect()->back()->with('success', $message);
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors du rejet de l\'avis de départ.');
        }
    }

    /**
     * Validate avis retour
     */
    public function validateAvisRetour(Request $request, AvisRetour $avisRetour)
    {
        try {
            $user = Auth::user();
            $demande = $avisRetour->avis->demande;
            $demandeUser = $demande->user;
            
            // Check if user is chef of demande user
            $isChefOfUser = function($validator, $demandeUser) {
                $chefEntiteIds = Entite::where('chef_ppr', $validator->ppr)->pluck('id');
                if ($chefEntiteIds->isEmpty()) {
                    return false;
                }
                
                $userParcours = Parcours::where('ppr', $demandeUser->ppr)
                    ->whereIn('entite_id', $chefEntiteIds)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->exists();
                
                return $userParcours;
            };
            
            // Generate PDF callback using service
            $generatePdfCallback = function($avisRetour, $demandeUser, $avisDepart) {
                try {
                    return $this->pdfService->generateAvisRetourPDF($avisRetour, $demandeUser, $avisDepart);
                } catch (\Exception $e) {
                    \Log::error('Error generating avis retour PDF: ' . $e->getMessage(), [
                        'avis_retour_id' => $avisRetour->id,
                        'exception' => get_class($e),
                    ]);
                    // Re-throw to prevent validation from continuing if main PDF fails
                    throw $e;
                }
            };
            
            // Generate explanation PDF callback using service
            $generateExplanationPdfCallback = function($avisRetour, $demandeUser, $avisDepart) {
                try {
                    return $this->pdfService->generateExplanationPDF($avisRetour, $demandeUser, $avisDepart);
                } catch (\Exception $e) {
                    \Log::error('Error generating explanation PDF: ' . $e->getMessage(), [
                        'avis_retour_id' => $avisRetour->id,
                        'exception' => get_class($e),
                    ]);
                    // Return null to allow validation to continue even if PDF generation fails
                    return null;
                }
            };
            
            // Get date_retour_effectif from request if provided
            $dateRetourEffectif = $request->input('date_retour_effectif');
            
            $validateAction = app(ValidateAvisRetourAction::class);
            $validateAction->execute($avisRetour, $user, $dateRetourEffectif, $isChefOfUser, $generatePdfCallback, $generateExplanationPdfCallback);
            
            return redirect()->back()->with('success', 'Avis de retour approuvé avec succès.');
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Error validating avis de retour: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'approbation de l\'avis de retour: ' . $e->getMessage());
        }
    }

    /**
     * Update date retour effectif
     */
    public function updateDateRetourEffectif(Request $request, AvisRetour $avisRetour)
    {
        try {
            $request->validate([
                'date_retour_effectif' => 'required|date',
            ]);
            
            $user = Auth::user();
            $demande = $avisRetour->avis->demande;
            $demandeUser = $demande->user;
            
            // Check if user is chef of demande user or admin
            $isChefOfUser = function($validator, $demandeUser) {
                $chefEntiteIds = Entite::where('chef_ppr', $validator->ppr)->pluck('id');
                if ($chefEntiteIds->isEmpty()) {
                    return false;
                }
                
                $userParcours = Parcours::where('ppr', $demandeUser->ppr)
                    ->whereIn('entite_id', $chefEntiteIds)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->exists();
                
                return $userParcours;
            };
            
            if (!$isChefOfUser($user, $demandeUser) && !$user->hasRole('admin')) {
                return redirect()->back()->with('error', 'Vous n\'avez pas l\'autorisation de modifier cette date de retour.');
            }
            
            $avisRetour->update([
                'date_retour_effectif' => $request->date_retour_effectif,
            ]);
            
            return redirect()->back()->with('success', 'Date de retour effectif mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de la date de retour effectif.');
        }
    }

    /**
     * Download explanation PDF
     */
    public function downloadExplanationPDF(AvisRetour $avisRetour)
    {
        $pdfPath = $avisRetour->explanation_pdf_path;
        
        // If PDF doesn't exist, generate it on the fly
        if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
            $avis = $avisRetour->avis;
            $demande = $avis ? $avis->demande : null;
            
            if (!$demande || !$demande->user) {
                abort(404, 'Demande ou utilisateur introuvable.');
            }
            
            $avisDepart = $avis ? $avis->avisDepart : null;
            
            // Check if explanation is needed (actual return date > declared return date)
            if ($avisRetour->date_retour_declaree && $avisRetour->date_retour_effectif) {
                $dateRetourDeclaree = Carbon::parse($avisRetour->date_retour_declaree);
                $dateRetourEffectif = Carbon::parse($avisRetour->date_retour_effectif);
                
                if ($dateRetourEffectif->greaterThan($dateRetourDeclaree)) {
                    // Generate PDF
                    $pdfPath = $this->pdfService->generateExplanationPDF($avisRetour, $demande->user, $avisDepart);
                    $avisRetour->update(['explanation_pdf_path' => $pdfPath]);
                } else {
                    abort(404, 'Note d\'explication non disponible. L\'explication n\'est requise que si la date de retour effective dépasse la date de retour déclarée.');
                }
            } else {
                abort(404, 'Note d\'explication non disponible. Les dates de retour sont manquantes.');
            }
        }
        
        $fullPath = Storage::disk('public')->path($pdfPath);
        $filename = 'note-explication-' . $avisRetour->id . '.pdf';
        
        return response()->download($fullPath, $filename);
    }

    /**
     * Download avis depart PDF
     */
    public function downloadAvisDepartPDF(AvisDepart $avisDepart)
    {
        try {
            $pdfPath = $avisDepart->pdf_path;
            
            if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
                // If PDF doesn't exist, generate it
                $avis = $avisDepart->avis;
                $demande = $avis ? $avis->demande : null;
                
                if (!$demande || !$demande->user) {
                    abort(404, 'Demande ou utilisateur introuvable.');
                }
                
                // Ensure user has necessary relationships loaded for PDF generation
                $user = $demande->user;
                if (!$user->relationLoaded('userInfo')) {
                    $user->load('userInfo.grade');
                }
                
                // Generate PDF with error handling
                try {
                    $pdfPath = $this->pdfService->generateAvisDepartPDF($avisDepart, $user);
                    $avisDepart->update(['pdf_path' => $pdfPath]);
                } catch (\Exception $e) {
                    \Log::error('Error generating avis de départ PDF in download: ' . $e->getMessage(), [
                        'avis_depart_id' => $avisDepart->id,
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    
                    // Determine redirect route based on user
                    $user = Auth::user();
                    $isOwner = $demande && $demande->ppr === $user->ppr;
                    $redirectRoute = $isOwner ? route('leaves.tracking') : route('hr.leaves.agents');
                    
                    return redirect($redirectRoute)
                        ->with('error', 'Erreur lors de la génération du PDF. Veuillez réessayer.');
                }
            }
            
            $fullPath = Storage::disk('public')->path($pdfPath);
            
            // Verify file exists before trying to serve it
            if (!file_exists($fullPath)) {
                \Log::error('PDF file not found after generation', [
                    'avis_depart_id' => $avisDepart->id,
                    'pdf_path' => $pdfPath,
                    'full_path' => $fullPath,
                ]);
                
                $user = Auth::user();
                $avis = $avisDepart->avis;
                $demande = $avis ? $avis->demande : null;
                $isOwner = $demande && $demande->ppr === $user->ppr;
                $redirectRoute = $isOwner ? route('leaves.tracking') : route('hr.leaves.agents');
                
                return redirect($redirectRoute)
                    ->with('error', 'Le fichier PDF est introuvable. Veuillez réessayer.');
            }
            
            $filename = 'avis-depart-' . $avisDepart->id . '.pdf';
            
            // Check if request wants inline viewing (from iframe)
            if (request()->get('inline') === '1') {
                return response()->file($fullPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]);
            }
            
            return response()->download($fullPath, $filename);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in downloadAvisDepartPDF: ' . $e->getMessage(), [
                'avis_depart_id' => $avisDepart->id,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $user = Auth::user();
            $avis = $avisDepart->avis;
            $demande = $avis ? $avis->demande : null;
            $isOwner = $demande && $demande->ppr === $user->ppr;
            $redirectRoute = $isOwner ? route('leaves.tracking') : route('hr.leaves.agents');
            
            return redirect($redirectRoute)
                ->with('error', 'Erreur lors de la génération du PDF. Veuillez réessayer.');
        }
    }

    /**
     * Download avis retour PDF
     */
    public function downloadAvisRetourPDF(AvisRetour $avisRetour)
    {
        try {
            $pdfPath = $avisRetour->pdf_path;
            
            if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
                // If PDF doesn't exist, generate it
                $avis = $avisRetour->avis;
                $demande = $avis ? $avis->demande : null;
                
                if (!$demande || !$demande->user) {
                    abort(404, 'Demande ou utilisateur introuvable.');
                }
                
                // Ensure user has necessary relationships loaded for PDF generation
                $user = $demande->user;
                if (!$user->relationLoaded('userInfo')) {
                    $user->load('userInfo.grade');
                }
                
                // Get avis de départ for PDF generation
                $avisDepart = $avis ? $avis->avisDepart : null;
                
                // Generate PDF with error handling
                try {
                    $pdfPath = $this->pdfService->generateAvisRetourPDF($avisRetour, $user, $avisDepart);
                    $avisRetour->update(['pdf_path' => $pdfPath]);
                } catch (\Exception $e) {
                    \Log::error('Error generating avis de retour PDF in download: ' . $e->getMessage(), [
                        'avis_retour_id' => $avisRetour->id,
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    
                    // Determine redirect route based on user
                    $user = Auth::user();
                    $isOwner = $demande && $demande->ppr === $user->ppr;
                    $redirectRoute = $isOwner ? route('leaves.tracking') : route('hr.leaves.agents');
                    
                    return redirect($redirectRoute)
                        ->with('error', 'Erreur lors de la génération du PDF. Veuillez réessayer.');
                }
            }
            
            $fullPath = Storage::disk('public')->path($pdfPath);
            
            // Verify file exists before trying to serve it
            if (!file_exists($fullPath)) {
                \Log::error('PDF file not found after generation', [
                    'avis_retour_id' => $avisRetour->id,
                    'pdf_path' => $pdfPath,
                    'full_path' => $fullPath,
                ]);
                
                $user = Auth::user();
                $avis = $avisRetour->avis;
                $demande = $avis ? $avis->demande : null;
                $isOwner = $demande && $demande->ppr === $user->ppr;
                $redirectRoute = $isOwner ? route('leaves.tracking') : route('hr.leaves.agents');
                
                return redirect($redirectRoute)
                    ->with('error', 'Le fichier PDF est introuvable. Veuillez réessayer.');
            }
            
            $filename = 'avis-retour-' . $avisRetour->id . '.pdf';
            
            // Check if request wants inline viewing (from iframe)
            if (request()->get('inline') === '1') {
                return response()->file($fullPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]);
            }
            
            return response()->download($fullPath, $filename);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in downloadAvisRetourPDF: ' . $e->getMessage(), [
                'avis_retour_id' => $avisRetour->id,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $user = Auth::user();
            $avis = $avisRetour->avis;
            $demande = $avis ? $avis->demande : null;
            $isOwner = $demande && $demande->ppr === $user->ppr;
            $redirectRoute = $isOwner ? route('leaves.tracking') : route('hr.leaves.agents');
            
            return redirect($redirectRoute)
                ->with('error', 'Erreur lors de la génération du PDF. Veuillez réessayer.');
        }
    }

    /**
     * View avis de départ PDF with solde info
     */
    public function viewAvisDepartPDF($avisDepart)
    {
        // Handle both route model binding and direct ID
        if (!($avisDepart instanceof AvisDepart)) {
            $avisDepart = AvisDepart::with(['avis.demande.user'])->find($avisDepart);
        } else {
            // Load relationships if not already loaded
            if (!$avisDepart->relationLoaded('avis')) {
                $avisDepart->load(['avis.demande.user']);
            }
        }
        
        if (!$avisDepart) {
            return redirect()->route('hr.leaves.agents')
                ->with('error', 'Avis de départ introuvable.');
        }
        
        $avis = $avisDepart->avis;
        $demande = $avis ? $avis->demande : null;
        
        if (!$demande || !$demande->user) {
            return redirect()->route('hr.leaves.agents')
                ->with('error', 'Demande ou utilisateur introuvable.');
        }

        $user = $demande->user;
        
        // Ensure user has necessary relationships loaded for PDF generation
        if (!$user->relationLoaded('userInfo')) {
            $user->load('userInfo.grade');
        }
        
        $currentUser = Auth::user();
        
        // Check if current user is the owner of the demande
        $isOwner = $currentUser && $currentUser->ppr === $demande->ppr;
        
        // Get solde info for the user
        $currentYear = Carbon::now()->year;
        $leaveData = app(\App\Actions\Conge\CalculateCongeBalanceAction::class)->execute($user);
        
        // Get PDF path, generate if it doesn't exist and avis is approved
        $pdfPath = $avisDepart->pdf_path;
        $pdfUrl = null;
        
        if ($avisDepart->statut === 'approved') {
            if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
                // Generate PDF if it doesn't exist
                try {
                    $pdfPath = $this->pdfService->generateAvisDepartPDF($avisDepart, $user);
                    $avisDepart->update(['pdf_path' => $pdfPath]);
                } catch (\Exception $e) {
                    \Log::error('Error generating avis de départ PDF in viewAvisDepartPDF: ' . $e->getMessage(), [
                        'avis_depart_id' => $avisDepart->id,
                        'user_ppr' => $user->ppr ?? null,
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't expose internal error details to users, but log them
                    \Log::error('Error generating avis de départ PDF in viewAvisDepartPDF: ' . $e->getMessage(), [
                        'avis_depart_id' => $avisDepart->id,
                        'user_ppr' => $user->ppr ?? null,
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    return redirect($isOwner ? route('leaves.tracking') : route('hr.leaves.agents'))
                        ->with('error', 'Erreur lors de la génération du PDF. Veuillez réessayer.');
                }
            }
            
            if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                // Use the download route with inline parameter for iframe viewing
                // This ensures proper URL generation and file serving
                $pdfUrl = route('hr.leaves.download-avis-depart-pdf', ['avisDepart' => $avisDepart->id, 'inline' => 1]);
            } elseif ($pdfPath) {
                // If path exists but not in storage, try to regenerate
                try {
                    $pdfPath = $this->pdfService->generateAvisDepartPDF($avisDepart, $user);
                    $avisDepart->update(['pdf_path' => $pdfPath]);
                    $pdfUrl = route('hr.leaves.download-avis-depart-pdf', ['avisDepart' => $avisDepart->id, 'inline' => 1]);
                } catch (\Exception $e) {
                    \Log::error('Error regenerating avis de départ PDF: ' . $e->getMessage());
                    $pdfUrl = null;
                }
            }
        }

        return view('leaves.view-avis-depart-pdf', compact('avisDepart', 'user', 'demande', 'leaveData', 'pdfUrl', 'isOwner'));
    }
    
    /**
     * Verify leave using verification code (public route, no auth required)
     */
    public function verifyLeave(Request $request, $code = null)
    {
        // Get code from query parameter or route parameter
        $code = $code ?? $request->get('code_verification');
        
        if (!$code) {
            return view('leaves.verify', [
                'error' => 'Code de vérification manquant.',
                'verified' => false,
            ]);
        }
        
        // Try to find avis de départ first
        $avisDepart = AvisDepart::where('verification_code', $code)->first();
        
        if ($avisDepart) {
            $avis = $avisDepart->avis;
            $demande = $avis ? $avis->demande : null;
            
            if (!$demande || !$demande->user) {
                return view('leaves.verify', [
                    'error' => 'Demande ou utilisateur introuvable.',
                    'verified' => false,
                ]);
            }
            
            $user = $demande->user;
            $avisRetour = $avis ? $avis->avisRetour : null;
            
            // Get leave balance data
            $currentYear = Carbon::now()->year;
            $leaveData = app(\App\Actions\Conge\CalculateCongeBalanceAction::class)->execute($user);
            
            return view('leaves.verify', [
                'verified' => true,
                'type' => 'depart',
                'user' => $user,
                'demande' => $demande,
                'avisDepart' => $avisDepart,
                'avisRetour' => $avisRetour,
                'leaveData' => $leaveData,
            ]);
        }
        
        // Try to find avis de retour
        $avisRetour = AvisRetour::where('verification_code', $code)->first();
        
        if ($avisRetour) {
            $avis = $avisRetour->avis;
            $demande = $avis ? $avis->demande : null;
            
            if (!$demande || !$demande->user) {
                return view('leaves.verify', [
                    'error' => 'Demande ou utilisateur introuvable.',
                    'verified' => false,
                ]);
            }
            
            $user = $demande->user;
            $avisDepart = $avis ? $avis->avisDepart : null;
            
            // Get leave balance data
            $currentYear = Carbon::now()->year;
            $leaveData = app(\App\Actions\Conge\CalculateCongeBalanceAction::class)->execute($user);
            
            return view('leaves.verify', [
                'verified' => true,
                'type' => 'retour',
                'user' => $user,
                'demande' => $demande,
                'avisDepart' => $avisDepart,
                'avisRetour' => $avisRetour,
                'leaveData' => $leaveData,
            ]);
        }
        
        return view('leaves.verify', [
            'error' => 'Code de vérification invalide ou introuvable.',
            'verified' => false,
        ]);
    }

    /**
     * View avis de retour PDF with solde info
     */
    public function viewAvisRetourPDF($avisRetour)
    {
        // Handle both route model binding and direct ID
        if (!($avisRetour instanceof AvisRetour)) {
            $avisRetour = AvisRetour::with(['avis.demande.user', 'avis.avisDepart'])->find($avisRetour);
        } else {
            // Load relationships if not already loaded
            if (!$avisRetour->relationLoaded('avis')) {
                $avisRetour->load(['avis.demande.user', 'avis.avisDepart']);
            }
        }
        
        if (!$avisRetour) {
            return redirect()->route('hr.leaves.agents')
                ->with('error', 'Avis de retour introuvable.');
        }
        
        $avis = $avisRetour->avis;
        $demande = $avis ? $avis->demande : null;
        
        if (!$demande || !$demande->user) {
            $currentUser = Auth::user();
            $isOwner = $currentUser && $demande && $currentUser->ppr === $demande->ppr;
            return redirect($isOwner ? route('leaves.tracking') : route('hr.leaves.agents'))
                ->with('error', 'Demande ou utilisateur introuvable.');
        }

        $user = $demande->user;
        
        // Ensure user has necessary relationships loaded for PDF generation
        if (!$user->relationLoaded('userInfo')) {
            $user->load('userInfo.grade');
        }
        
        $avisDepart = $avis ? $avis->avisDepart : null;
        $currentUser = Auth::user();
        
        // Check if current user is the owner of the demande
        $isOwner = $currentUser && $currentUser->ppr === $demande->ppr;
        
        // Get solde info for the user
        $currentYear = Carbon::now()->year;
        $leaveData = app(\App\Actions\Conge\CalculateCongeBalanceAction::class)->execute($user);
        
        // Get PDF path, generate if it doesn't exist and avis is approved
        $pdfPath = $avisRetour->pdf_path;
        $pdfUrl = null;
        
        if ($avisRetour->statut === 'approved') {
            if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
                // Generate PDF if it doesn't exist
                try {
                    $pdfPath = $this->pdfService->generateAvisRetourPDF($avisRetour, $user, $avisDepart);
                    $avisRetour->update(['pdf_path' => $pdfPath]);
                } catch (\Exception $e) {
                    \Log::error('Error generating avis de retour PDF: ' . $e->getMessage(), [
                        'avis_retour_id' => $avisRetour->id,
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    return redirect($isOwner ? route('leaves.tracking') : route('hr.leaves.agents'))
                        ->with('error', 'Erreur lors de la génération du PDF. Veuillez réessayer.');
                }
            }
            
            // Use the download route with inline parameter for iframe viewing
            $pdfUrl = route('hr.leaves.download-avis-retour-pdf', ['avisRetour' => $avisRetour->id, 'inline' => 1]);
        }

        return view('leaves.view-avis-retour-pdf', compact('avisRetour', 'avisDepart', 'user', 'demande', 'leaveData', 'pdfUrl', 'isOwner'));
    }

    /**
     * Show user info
     */
    public function showUserInfo($ppr)
    {
        // Fetch user with relationships
        $user = User::where('ppr', $ppr)
            ->with(['userInfo.grade'])
            ->first();
        
        if (!$user) {
            abort(404, 'Utilisateur introuvable.');
        }
        
        // Fetch parcours for the user
        $parcours = Parcours::where('ppr', $ppr)
            ->with(['entite.parent', 'grade'])
            ->orderBy('date_debut', 'desc')
            ->get();
        
        return view('leaves.user-info', compact('user', 'parcours'));
    }

    /**
     * Show leaves statistics (admin only)
     */
    public function leavesStats()
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        // Total leave requests
        $totalDemandes = Demande::where('type', 'conge')->count();
        
        // Pending leave requests (avis de départ pending)
        $pendingDemandes = Demande::where('type', 'conge')
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'pending');
            })->count();
        
        // Approved leave requests
        $approvedDemandes = Demande::where('type', 'conge')
            ->where('statut', 'approved')->count();
        
        // Rejected leave requests
        $rejectedDemandes = Demande::where('type', 'conge')
            ->where('statut', 'rejected')->count();
        
        // Cancelled leave requests
        $cancelledDemandes = Demande::where('type', 'conge')
            ->where('statut', 'cancelled')->count();

        // Leave requests by type
        $leavesByType = DB::table('demande_conges')
            ->join('type_conges', 'demande_conges.type_conge_id', '=', 'type_conges.id')
            ->select('type_conges.name', DB::raw('count(*) as count'))
            ->groupBy('type_conges.name')
            ->orderBy('count', 'desc')
            ->get();

        // Leave requests by status
        $leavesByStatus = [
            'En attente' => $pendingDemandes,
            'Approuvées' => $approvedDemandes,
            'Rejetées' => $rejectedDemandes,
            'Annulées' => $cancelledDemandes,
        ];

        // Monthly trends (last 6 months)
        $monthlyLeaves = [];
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthlyLeaves[] = Demande::where('type', 'conge')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Recent leave requests (last 10)
        $recentLeaves = Demande::where('type', 'conge')
            ->with(['user', 'demandeConge.typeConge'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics by entity type (Central vs Regional)
        // Get central entity IDs
        $centralEntiteIds = Entite::where('type', 'Central')->pluck('id');
        
        // Get users with active parcours in central entities
        $centralUserPprs = Parcours::whereIn('entite_id', $centralEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->distinct()
            ->pluck('ppr');
        
        $centralLeaves = Demande::where('type', 'conge')
            ->whereIn('ppr', $centralUserPprs)
            ->count();
        
        // Get regional entity IDs
        $regionalEntiteIds = Entite::where('type', 'Régional')->pluck('id');
        
        // Get users with active parcours in regional entities
        $regionalUserPprs = Parcours::whereIn('entite_id', $regionalEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->distinct()
            ->pluck('ppr');
        
        $regionalLeaves = Demande::where('type', 'conge')
            ->whereIn('ppr', $regionalUserPprs)
            ->count();

        // This year statistics
        $leavesThisYear = Demande::where('type', 'conge')
            ->whereYear('created_at', now()->year)
            ->count();
        
        $leavesThisMonth = Demande::where('type', 'conge')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Approved leaves this year
        $approvedThisYear = Demande::where('type', 'conge')
            ->where('statut', 'approved')
            ->whereYear('created_at', now()->year)
            ->count();

        // Rejected leaves this year
        $rejectedThisYear = Demande::where('type', 'conge')
            ->where('statut', 'rejected')
            ->whereYear('created_at', now()->year)
            ->count();

        return view('leaves.stats', compact(
            'totalDemandes',
            'pendingDemandes',
            'approvedDemandes',
            'rejectedDemandes',
            'cancelledDemandes',
            'leavesByType',
            'leavesByStatus',
            'monthlyLeaves',
            'months',
            'recentLeaves',
            'centralLeaves',
            'regionalLeaves',
            'leavesThisYear',
            'leavesThisMonth',
            'approvedThisYear',
            'rejectedThisYear'
        ));
    }


    /**
     * Notify the chef when a collaborateur submits a new leave request
     */
    protected function notifyChefAboutLeaveRequest(Demande $demande, AvisDepart $avisDepart, ?Parcours $parcours = null): void
    {
        // Get the collaborateur's current entity to find their chef
        if (!$parcours) {
            $parcours = Parcours::where('ppr', $demande->ppr)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', now());
                })
                ->orderBy('date_debut', 'desc')
                ->first();
        }

        if (!$parcours || !$parcours->entite_id) {
            return;
        }

        // Find the entity and traverse up the hierarchy to find the chef
        $entite = Entite::with('parent.parent.parent.parent.parent.parent.parent.parent.parent.parent')
            ->find($parcours->entite_id);
        
        if (!$entite) {
            return;
        }

        // Find chef along entity hierarchy using chef_ppr
        $current = $entite;
        $maxDepth = 10;
        $depth = 0;
        $chefPpr = null;

        while ($current && $depth < $maxDepth) {
            if ($current->chef_ppr && $current->chef_ppr !== $demande->ppr) {
                $chefPpr = $current->chef_ppr;
                break;
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

        if (!$chefPpr) {
            return;
        }

        // Get the chef user
        $chef = User::where('ppr', $chefPpr)->first();
        if (!$chef) {
            return;
        }

        // Get the employee user
        $employee = User::where('ppr', $demande->ppr)->first();
        if (!$employee) {
            return;
        }

        // Create notification for the chef
        $notificationService = app(NotificationService::class);
        $notificationService->sendToUser(
            $chef,
            'leave_request',
            'Nouvelle demande de congé',
            $employee->name . ' a soumis une nouvelle demande de congé qui nécessite votre validation.',
            [
                'demande_id' => $demande->id,
                'employee_name' => $employee->name,
                'employee_ppr' => $employee->ppr,
                'date_depart' => $avisDepart->date_depart,
                'date_retour' => $avisDepart->date_retour,
                'nb_jours' => $avisDepart->nb_jours_demandes,
            ],
            [
                'action_url' => route('hr.leaves.agents', ['statut' => 'pending']),
                'icon' => 'fas fa-calendar-alt',
                'color' => 'info',
                'priority' => 'high',
            ]
        );

        // Optionally send email notification
        try {
            Mail::to($chef->email)->send(new LeaveRequestNotification($employee, $chef, $demande, $avisDepart));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send leave request email notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify the chef when a collaborateur declares an avis de retour
     */
    protected function notifyChefAboutAvisRetour(Demande $demande, AvisRetour $avisRetour): void
    {
        // Get the collaborateur's current entity to find their chef
        $currentParcours = Parcours::where('ppr', $demande->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->orderBy('date_debut', 'desc')
            ->first();

        if (!$currentParcours || !$currentParcours->entite_id) {
            return;
        }

        // Find the entity and get the chef
        $entite = Entite::find($currentParcours->entite_id);
        if (!$entite || !$entite->chef_ppr) {
            return;
        }

        // Create an alert for the chef (using DismissedAlert pattern but for notification)
        // We'll check for this in the dashboard
        // Note: We don't create a DismissedAlert here, we'll check for recent avis_retour in dashboard
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

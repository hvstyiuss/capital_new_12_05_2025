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
use App\Mail\LeaveRequestNotification;
use App\Actions\AvisDepart\ValidateAvisDepartAction;
use App\Actions\AvisDepart\RejectAvisDepartAction;
use App\Actions\AvisRetour\ValidateAvisRetourAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DomainException;

class LeaveController extends Controller
{
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
                'consumption_exceeds' => false, // Can be calculated if needed
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
            $nbrJoursConsumes = $dateDepart->diffInDays($dateRetour) + 1; // +1 to include both days
        }
        
        // Create avis retour
        AvisRetour::create([
            'avis_id' => $request->avis_id,
            'date_retour_declaree' => $request->date_retour_declaree,
            'date_retour_effectif' => $request->date_retour_effectif ?? $request->date_retour_declaree,
            'nbr_jours_consumes' => $nbrJoursConsumes ?? 0,
            'statut' => 'pending',
        ]);
        
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
            AvisDepart::create([
                'avis_id' => $avis->id,
                'nb_jours_demandes' => $request->nb_jours_demandes,
                'date_depart' => $request->date_depart,
                'date_retour' => $request->date_retour,
                'statut' => 'pending', // Needs chef approval
            ]);

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
        // Implementation needed - placeholder
        return redirect()->route('hr.leaves.index');
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
            
            // Generate PDF callback
            $generatePdfCallback = function($avisDepart, $user) {
                // Use reflection to access private method if it exists
                if (method_exists($this, 'generateAvisDepartPDF')) {
                    $reflection = new \ReflectionClass($this);
                    $method = $reflection->getMethod('generateAvisDepartPDF');
                    $method->setAccessible(true);
                    return $method->invoke($this, $avisDepart, $user);
                }
                return null;
            };
            
            $validateAction = app(ValidateAvisDepartAction::class);
            $validateAction->execute($avisDepart, $user, $isChefOfUser, $generatePdfCallback);
            
            return redirect()->back()->with('success', 'Avis de départ approuvé avec succès.');
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'approbation de l\'avis de départ.');
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
            
            // Generate PDF callback (if method exists)
            $generatePdfCallback = function($avisRetour, $demandeUser, $avisDepart) {
                // Use reflection to access private method if it exists
                if (method_exists($this, 'generateAvisRetourPDF')) {
                    $reflection = new \ReflectionClass($this);
                    $method = $reflection->getMethod('generateAvisRetourPDF');
                    $method->setAccessible(true);
                    return $method->invoke($this, $avisRetour, $demandeUser, $avisDepart);
                }
                return null;
            };
            
            // Generate explanation PDF callback (if method exists)
            $generateExplanationPdfCallback = function($avisRetour, $demandeUser, $avisDepart) {
                // Use reflection to access private method if it exists
                if (method_exists($this, 'generateExplanationPDF')) {
                    $reflection = new \ReflectionClass($this);
                    $method = $reflection->getMethod('generateExplanationPDF');
                    $method->setAccessible(true);
                    return $method->invoke($this, $avisRetour, $demandeUser, $avisDepart);
                }
                return null;
            };
            
            // Get date_retour_effectif from request if provided
            $dateRetourEffectif = $request->input('date_retour_effectif');
            
            $validateAction = app(ValidateAvisRetourAction::class);
            $validateAction->execute($avisRetour, $user, $dateRetourEffectif, $isChefOfUser, $generatePdfCallback, $generateExplanationPdfCallback);
            
            return redirect()->back()->with('success', 'Avis de retour approuvé avec succès.');
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'approbation de l\'avis de retour.');
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
        // Implementation needed - placeholder
        return response()->download('');
    }

    /**
     * Download avis depart PDF
     */
    public function downloadAvisDepartPDF(AvisDepart $avisDepart)
    {
        // Implementation needed - placeholder
        return response()->download('');
    }

    /**
     * Download avis retour PDF
     */
    public function downloadAvisRetourPDF(AvisRetour $avisRetour)
    {
        // Implementation needed - placeholder
        return response()->download('');
    }

    /**
     * Show user info
     */
    public function showUserInfo($ppr)
    {
        // Implementation needed - placeholder
        return view('leaves.user-info');
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
}

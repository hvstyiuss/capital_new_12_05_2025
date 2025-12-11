<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Demande;
use App\Models\Parcours;
use App\Models\Entite;
use Carbon\Carbon;

class LeaveTrackingController extends Controller
{
    /**
     * Display the leave request tracking page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $year = $request->get('year', date('Y'));
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        
        // Check if user is a chef (for display purposes only)
        $isChef = Entite::where('chef_ppr', $user->ppr)->exists();
        
        // Personal tracking page: always show only the logged-in user's own demandes
        // Chefs can see their collaborators' demandes on the "Suivi de demandes de mes agents" page
        $query = Demande::with(['avis.avisDepart', 'avis.avisRetour', 'user'])
            ->where('ppr', $user->ppr);
        
        // Get all demandes (for frontend filtering) - limit to last 10 years
        $allDemandesQuery = Demande::with(['avis.avisDepart', 'avis.avisRetour', 'user'])
            ->where('ppr', $user->ppr)
            ->whereYear('created_at', '>=', date('Y') - 10)
            ->orderBy('created_at', 'desc');
        
        $allDemandes = $allDemandesQuery->get();
        
        // Transform all demandes for frontend
        $allDemandesData = $allDemandes->map(function($demande) use ($user) {
            $avis = $demande->avis;
            $avisDepart = $avis ? $avis->avisDepart : null;
            $avisRetour = $avis ? $avis->avisRetour : null;
            
            // Calculate number of days requested
            $nbrJours = 0;
            if ($avisDepart && $avisDepart->nb_jours_demandes > 0) {
                $nbrJours = $avisDepart->nb_jours_demandes;
            } elseif ($avisDepart && $avisDepart->date_depart && $avisDepart->date_retour) {
                $start = Carbon::parse($avisDepart->date_depart);
                $end = Carbon::parse($avisDepart->date_retour);
                $current = $start->copy();
                $workingDays = 0;
                
                $holidays = \App\Models\JoursFerie::whereBetween('date', [$start, $end])
                    ->pluck('date')
                    ->map(function($date) {
                        return $date->format('Y-m-d');
                    })
                    ->toArray();
                
                while ($current->lte($end)) {
                    $dateString = $current->format('Y-m-d');
                    if (!$current->isWeekend() && !in_array($dateString, $holidays)) {
                        $workingDays++;
                    }
                    $current->addDay();
                }
                
                $nbrJours = $workingDays;
            }
            
            $statutMap = [
                'pending' => 'En attente',
                'approved' => 'Validé',
                'rejected' => 'Rejeté',
                'cancelled' => 'Annulé',
            ];
            $statut = $avisDepart ? ($statutMap[$avisDepart->statut] ?? $avisDepart->statut) : 'En attente';
            
            return [
                'id' => $demande->id,
                'ppr' => $demande->ppr,
                'user_name' => $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A',
                'type' => 'Congé Administratif Annuel',
                'date_depot' => $demande->created_at->toISOString(),
                'nbr_jours' => $nbrJours,
                'date_depart' => $avisDepart ? ($avisDepart->date_depart ? Carbon::parse($avisDepart->date_depart)->toISOString() : null) : null,
                'date_retour' => $avisDepart ? ($avisDepart->date_retour ? Carbon::parse($avisDepart->date_retour)->toISOString() : null) : null,
                'statut' => $statut,
                'is_own' => $demande->ppr === $user->ppr,
                'avis_depart' => $avisDepart ? [
                    'date_depot' => $avisDepart->created_at ? $avisDepart->created_at->toISOString() : null,
                    'nbr_jours_consommes' => $avisRetour ? $avisRetour->nbr_jours_consumes : ($avisDepart->nb_jours_demandes ?? 0),
                    'date_retour_declaree' => $avisRetour ? ($avisRetour->date_retour_declaree ? Carbon::parse($avisRetour->date_retour_declaree)->toISOString() : null) : null,
                    'date_retour_effectif' => $avisRetour ? ($avisRetour->date_retour_effectif ? Carbon::parse($avisRetour->date_retour_effectif)->toISOString() : null) : null,
                    'statut' => $avisDepart->statut,
                    'id' => $avisDepart->id,
                    'pdf_path' => $avisDepart->pdf_path ?? null,
                ] : null,
                'avis_retour' => $avisRetour ? [
                    'statut' => $statutMap[$avisRetour->statut] ?? $avisRetour->statut,
                    'statut_raw' => $avisRetour->statut,
                    'id' => $avisRetour->id,
                    'date_depot' => $avisRetour->created_at ? $avisRetour->created_at->toISOString() : null,
                    'nbr_jours_consommes' => $avisRetour->nbr_jours_consumes,
                    'date_retour_declaree' => $avisRetour->date_retour_declaree ? Carbon::parse($avisRetour->date_retour_declaree)->toISOString() : null,
                    'date_retour_effectif' => $avisRetour->date_retour_effectif ? Carbon::parse($avisRetour->date_retour_effectif)->toISOString() : null,
                    'pdf_path' => $avisRetour->pdf_path,
                    'explanation_pdf_path' => $avisRetour->explanation_pdf_path,
                ] : null,
            ];
        });
        
        // Get paginated results for initial display (backward compatibility)
        $query->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc');
        
        // Apply search filter if provided (search in avis de départ statut)
        if ($search) {
            $query->whereHas('avis.avisDepart', function($q) use ($search) {
                $q->where('statut', 'like', '%' . $search . '%');
            });
        }
        
        // Get paginated results
        $demandesPaginated = $query->paginate($perPage, ['*'], 'page', $request->get('page', 1));
        
        // Transform the data to match the view structure
        $demandes = $demandesPaginated->getCollection()->map(function($demande) use ($user) {
            $avis = $demande->avis;
            $avisDepart = $avis ? $avis->avisDepart : null;
            $avisRetour = $avis ? $avis->avisRetour : null;
            
            // Calculate number of days requested
            // Always use nb_jours_demandes (working days) when available, as it excludes weekends and holidays
            $nbrJours = 0;
            if ($avisDepart && $avisDepart->nb_jours_demandes > 0) {
                $nbrJours = $avisDepart->nb_jours_demandes;
            } elseif ($avisDepart && $avisDepart->date_depart && $avisDepart->date_retour) {
                // Fallback: calculate working days between dates (excluding weekends and holidays)
                $start = Carbon::parse($avisDepart->date_depart);
                $end = Carbon::parse($avisDepart->date_retour);
                $current = $start->copy();
                $workingDays = 0;
                
                // Get holidays in the date range
                $holidays = \App\Models\JoursFerie::whereBetween('date', [$start, $end])
                    ->pluck('date')
                    ->map(function($date) {
                        return $date->format('Y-m-d');
                    })
                    ->toArray();
                
                // Count only working days (excluding weekends and holidays)
                while ($current->lte($end)) {
                    $dateString = $current->format('Y-m-d');
                    if (!$current->isWeekend() && !in_array($dateString, $holidays)) {
                        $workingDays++;
                    }
                    $current->addDay();
                }
                
                $nbrJours = $workingDays;
            }
            
            // Map statut to French (use avis de départ statut)
            $statutMap = [
                'pending' => 'En attente',
                'approved' => 'Validé',
                'rejected' => 'Rejeté',
                'cancelled' => 'Annulé',
            ];
            $statut = $avisDepart ? ($statutMap[$avisDepart->statut] ?? $avisDepart->statut) : 'En attente';
            
            return [
                'id' => $demande->id,
                'ppr' => $demande->ppr,
                'user_name' => $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A',
                'type' => 'Congé Administratif Annuel', // Default type, can be enhanced later
                'date_depot' => $demande->created_at,
                'nbr_jours' => $nbrJours,
                'date_depart' => $avisDepart ? $avisDepart->date_depart : null,
                'date_retour' => $avisDepart ? $avisDepart->date_retour : null,
                'statut' => $statut,
                'is_own' => $demande->ppr === $user->ppr,
                'avis_depart' => $avisDepart ? [
                    'date_depot' => $avisDepart->created_at,
                    'nbr_jours_consommes' => $avisRetour ? $avisRetour->nbr_jours_consumes : ($avisDepart->nb_jours_demandes ?? 0),
                    'date_retour_declaree' => $avisRetour ? $avisRetour->date_retour_declaree : null,
                    'date_retour_effectif' => $avisRetour ? $avisRetour->date_retour_effectif : null,
                    'statut' => $avisDepart->statut,
                    'id' => $avisDepart->id,
                    'pdf_path' => $avisDepart->pdf_path ?? null,
                ] : null,
                'avis_retour' => $avisRetour ? [
                    'statut' => $statutMap[$avisRetour->statut] ?? $avisRetour->statut,
                    'statut_raw' => $avisRetour->statut,
                    'id' => $avisRetour->id,
                    'date_depot' => $avisRetour->created_at,
                    'nbr_jours_consommes' => $avisRetour->nbr_jours_consumes,
                    'date_retour_declaree' => $avisRetour->date_retour_declaree,
                    'date_retour_effectif' => $avisRetour->date_retour_effectif,
                    'pdf_path' => $avisRetour->pdf_path,
                    'explanation_pdf_path' => $avisRetour->explanation_pdf_path,
                ] : null,
            ];
        });
        
        // Create a custom paginator
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $demandes,
            $demandesPaginated->total(),
            $perPage,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('leaves.tracking', [
            'items' => $items,
            'year' => $year,
            'perPage' => $perPage,
            'search' => $search,
            'currentPage' => $items->currentPage(),
            'lastPage' => $items->lastPage(),
            'total' => $items->total(),
            'isChef' => $isChef || $user->hasRole('admin'),
            'allDemandesData' => $allDemandesData,
        ]);
    }
}


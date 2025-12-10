<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entite;
use App\Models\EntiteInfo;
use App\Models\DeplacementPeriode;
use App\Models\DeplacementIn;
use App\Models\Deplacement;
use App\Models\User;
use App\Models\HorsBareme;
use App\Models\EchelleTarif;
use App\Services\DeplacementPreparationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class DeplacementController extends Controller
{
    protected DeplacementPreparationService $preparationService;

    public function __construct(DeplacementPreparationService $preparationService)
    {
        $this->preparationService = $preparationService;
    }
    /**
     * Show entities by type (central or regional).
     */
    public function showByType($type)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($type, ['central', 'regional'])) {
            abort(404, 'Type not found.');
        }

        $periodes = DeplacementPeriode::orderBy('name')->get();

        return view('deplacements.by-type', compact('type', 'periodes'));
    }

    /**
     * Show entities for a specific type and period.
     */
    public function showByPeriod($type, $periodeId)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($type, ['central', 'regional'])) {
            abort(404, 'Type not found.');
        }

        $periode = DeplacementPeriode::findOrFail($periodeId);

        // Get entities of this type that have users with deplacements for this period
        $entites = Entite::whereHas('entiteInfo', function($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereHas('parcours', function($query) {
                $query->where(function($q) {
                    $q->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
                });
            })
            ->with('entiteInfo')
            ->orderBy('name')
            ->get()
            ->filter(function($entite) use ($periodeId) {
                // Check if this entity has any deplacement_ins for this period
                $userPprs = DB::table('parcours')
                    ->where('entite_id', $entite->id)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->distinct()
                    ->pluck('ppr');
                
                $deplacementIds = DB::table('deplacements')
                    ->whereIn('ppr', $userPprs)
                    ->pluck('id');
                
                return DB::table('deplacement_ins')
                    ->where('deplacement_periode_id', $periodeId)
                    ->whereIn('deplacement_id', $deplacementIds)
                    ->exists();
            });

        return view('deplacements.by-period', compact('type', 'periode', 'entites'));
    }

    /**
     * Show deplacement_ins data for a specific entity and period.
     */
    public function showByEntity(Request $request, $type, $periodeId, $entiteId)
    {
        $user = auth()->user();
        $entite = Entite::with('entiteInfo')->findOrFail($entiteId);
        
        // Check if user is admin/HR or chef of this entity
        $isAuthorized = $user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']) 
                     || $entite->chef_ppr === $user->ppr;
        
        if (!$isAuthorized) {
            abort(403, 'Unauthorized.');
        }

        $periode = DeplacementPeriode::findOrFail($periodeId);
        $entite = Entite::with('entiteInfo')->findOrFail($entiteId);

        // Verify entity type matches (only for admin/HR, chefs can access regardless)
        if ($user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            if (!$entite->entiteInfo || $entite->entiteInfo->type !== $type) {
                abort(404, 'Entity type mismatch.');
            }
        }

        // Get year filter (default to current year)
        $selectedYear = $request->input('year', now()->year);
        
        // Get all users in this entity first
        $userPprs = DB::table('parcours')
            ->where('entite_id', $entiteId)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->distinct()
            ->pluck('ppr');
        
        // Get available years from deplacements for this entity and periode
        $availableYears = Deplacement::whereIn('ppr', $userPprs)
            ->whereHas('deplacementIns', function($query) use ($periodeId) {
                $query->where('deplacement_periode_id', $periodeId);
            })
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee')
            ->filter()
            ->toArray();
        
        // If no years found, add current year
        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }
        
        // Ensure selected year is in available years
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $availableYears[0] ?? now()->year;
        }

        // Get deplacement_ins for this entity and period, filtered by year

        // Then get deplacements for these users, filtered by year
        $deplacementIds = Deplacement::whereIn('ppr', $userPprs)
            ->where('annee', $selectedYear)
            ->pluck('id');

        // Get deplacement_ins for these deplacements and the period
        $deplacementInsQuery = DeplacementIn::where('deplacement_periode_id', $periodeId)
            ->whereIn('deplacement_id', $deplacementIds)
            ->with([
                'deplacement.user.userInfo.grade.Echelle',
                'deplacement.user.parcours' => function($query) use ($entiteId) {
                    $query->where('entite_id', $entiteId)
                          ->where(function($q) {
                              $q->whereNull('date_fin')
                                ->orWhere('date_fin', '>=', now());
                          })
                          ->orderBy('date_debut', 'desc');
                },
                'deplacement.echelleTarif',
                'periode'
            ]);

        // Group by user, year, and periode to avoid duplicates
        // Get unique combinations of (ppr, annee, periode_id)
        $deplacementIns = $deplacementInsQuery->get();
        
        // Group by user and calculate aggregated data
        $groupedData = [];
        foreach ($deplacementIns as $deplacementIn) {
            $deplacement = $deplacementIn->deplacement;
            $ppr = $deplacement->ppr;
            $annee = $deplacement->annee;
            $key = "{$ppr}_{$annee}_{$periodeId}";
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'ppr' => $ppr,
                    'annee' => $annee,
                    'user' => $deplacement->user,
                    'deplacement_ins' => [],
                    'total_nbr_jours' => 0,
                    'montant_deplacement' => 0,
                    'total_somme' => 0,
                    'echelle' => null,
                ];
                
                // Get echelle for display
                if ($deplacement->user && $deplacement->user->userInfo && $deplacement->user->userInfo->grade && $deplacement->user->userInfo->grade->Echelle) {
                    $groupedData[$key]['echelle'] = $deplacement->user->userInfo->grade->Echelle;
                }
            }
            
            // Sum up nbr_jours (sum of all months)
            $groupedData[$key]['total_nbr_jours'] += $deplacement->nbr_jours ?? 0;
            
            // Sum up somme from each deplacement (already calculated: nbr_jours * montant_deplacement)
            if ($deplacement->somme) {
                $groupedData[$key]['total_somme'] += (float) $deplacement->somme;
            } else {
                // If somme is not set, try to calculate it from echelle_tarif
                $tarif = $deplacement->echelleTarif;
                if (!$tarif && $deplacement->echelle_tarifs_id) {
                    // Try to load the tarif if not already loaded
                    $tarif = EchelleTarif::find($deplacement->echelle_tarifs_id);
                }
                
                // If still no tarif, try to get it from user's echelle
                if (!$tarif && $groupedData[$key]['echelle']) {
                    $tarif = EchelleTarif::where('echelle_id', $groupedData[$key]['echelle']->id)
                        ->where('type_in_out_mission', 'in')
                        ->first();
                }
                
                if ($tarif && $tarif->montant_deplacement) {
                    $montantDeplacement = (float) $tarif->montant_deplacement;
                    $groupedData[$key]['montant_deplacement'] = $montantDeplacement;
                    // Calculate somme for this deplacement
                    $somme = ($deplacement->nbr_jours ?? 0) * $montantDeplacement;
                    $groupedData[$key]['total_somme'] += $somme;
                }
            }
            
            $groupedData[$key]['deplacement_ins'][] = $deplacementIn;
        }
        
        // If total_somme is still 0, try to calculate from total_nbr_jours * montant_deplacement
        foreach ($groupedData as &$group) {
            if ($group['total_somme'] == 0 && $group['total_nbr_jours'] > 0) {
                // Try to get montant_deplacement from echelle
                if (!$group['montant_deplacement'] && $group['echelle']) {
                    $tarif = EchelleTarif::where('echelle_id', $group['echelle']->id)
                        ->where('type_in_out_mission', 'in')
                        ->first();
                    
                    if ($tarif && $tarif->montant_deplacement) {
                        $group['montant_deplacement'] = (float) $tarif->montant_deplacement;
                    }
                }
                
                // Calculate total_somme if we have montant_deplacement
                if ($group['montant_deplacement'] > 0) {
                    $group['total_somme'] = $group['total_nbr_jours'] * $group['montant_deplacement'];
                }
            }
        }
        
        // Convert to collection and paginate
        $groupedCollection = collect($groupedData);
        
        // Paginate manually
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $items = $groupedCollection->forPage($currentPage, $perPage);
        $total = $groupedCollection->count();
        
        $paginatedData = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Calculate statistics from grouped data
        $totalSomme = $groupedCollection->sum('total_somme');
        $totalBeneficiaires = $groupedCollection->count();
        $totalBeneficiairesPossible = $entite->parcours()
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->distinct('ppr')
            ->count('ppr');

        return view('deplacements.by-entity', compact(
            'type', 
            'periode', 
            'entite', 
            'paginatedData',
            'totalSomme',
            'totalBeneficiaires',
            'totalBeneficiairesPossible',
            'selectedYear',
            'availableYears'
        ));
    }

    /**
     * Show deplacement periods for chef's entities.
     */
    public function chefIndex()
    {
        $user = auth()->user();
        
        if (!$user->isChef()) {
            abort(403, 'Seuls les chefs peuvent accéder à cette page.');
        }

        // Get entities where user is chef
        $entites = Entite::where('chef_ppr', $user->ppr)
            ->with('entiteInfo')
            ->get();

        if ($entites->isEmpty()) {
            return view('deplacements.chef-no-entities');
        }

        $periodes = DeplacementPeriode::orderBy('name')->get();

        return view('deplacements.chef-index', compact('entites', 'periodes'));
    }

    /**
     * Show preparation page for a periode (chef only).
     */
    public function preparerPeriode($type, $periodeId, $entiteId)
    {
        $user = auth()->user();
        $entite = Entite::with('entiteInfo')->findOrFail($entiteId);
        
        // Only chefs (not admins) can prepare periode
        if ($entite->chef_ppr !== $user->ppr) {
            abort(403, 'Seuls les chefs de l\'entité peuvent préparer les déplacements.');
        }
        
        // Prevent admins from preparing periode
        if ($user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Les administrateurs ne peuvent pas préparer les périodes.');
        }

        $periode = DeplacementPeriode::findOrFail($periodeId);
        $currentYear = now()->year;
        
        // Check if deplacements already exist for this periode, year, and entite
        $userPprs = User::whereHas('parcours', function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })->pluck('ppr');
        
        $existingDeplacements = Deplacement::whereIn('ppr', $userPprs)
            ->where('annee', $currentYear)
            ->whereHas('deplacementIns', function($query) use ($periodeId) {
                $query->where('deplacement_periode_id', $periodeId);
            })
            ->exists();
        
        if ($existingDeplacements) {
            return redirect()->back()->with('error', 'Des déplacements existent déjà pour cette période et cette année. Vous ne pouvez préparer qu\'une seule fois par trimestre par année.');
        }
        $months = $this->preparationService->getTrimestreMonths($periodeId);
        
        // Validate that months array is not empty
        if (empty($months)) {
            return redirect()->back()->with('error', 'La période sélectionnée n\'a pas de mois associés. Veuillez vérifier la configuration de la période.');
        }
        
        // Get all users in this entity
        $users = User::whereHas('parcours', function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })
        ->with(['userInfo.grade.Echelle', 'parcours' => function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        }])
        ->get();

        $agentsData = [];
        foreach ($users as $user) {
            $agentData = [
                'user' => $user,
                'months' => [],
                'conges' => [],
                'max_jours' => $this->preparationService->getMaxJoursForUser($user, $periodeId),
            ];
            
            // Only calculate dates if months array is not empty
            if (!empty($months)) {
                $startDate = Carbon::create($currentYear, $months[0], 1);
                $endDate = Carbon::create($currentYear, end($months), 1)->endOfMonth();
                
                // Get conges for the period
                $agentData['conges'] = $this->preparationService->getCongesForPeriod($user, $startDate, $endDate);
                
                // Get available days for each month
                foreach ($months as $month) {
                    $agentData['months'][$month] = $this->preparationService->getAvailableDaysForMonth(
                        $user, 
                        $currentYear, 
                        $month, 
                        $periodeId
                    );
                }
            }
            
            $agentsData[] = $agentData;
        }

        return view('deplacements.preparer-periode', compact(
            'type', 
            'periode', 
            'entite', 
            'agentsData', 
            'months',
            'currentYear'
        ));
    }

    /**
     * Download Excel for preparation.
     */
    public function downloadExcel($type, $periodeId, $entiteId)
    {
        // This will be implemented with Maatwebsite Excel
        // For now, return a simple response
        return response()->json(['message' => 'Excel export will be implemented']);
    }

    /**
     * Start the process - Step 1: Choose days per month.
     */
    public function startProcess(Request $request, $type, $periodeId, $entiteId)
    {
        $user = auth()->user();
        $entite = Entite::findOrFail($entiteId);
        
        // Only chefs (not admins) can start process
        if ($entite->chef_ppr !== $user->ppr) {
            abort(403, 'Unauthorized.');
        }
        
        // Prevent admins from starting process
        if ($user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Les administrateurs ne peuvent pas démarrer le processus de préparation.');
        }

        $periode = DeplacementPeriode::findOrFail($periodeId);
        $currentYear = now()->year;
        
        // Check if deplacements already exist for this periode, year, and entite
        $userPprs = User::whereHas('parcours', function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })->pluck('ppr');
        
        $existingDeplacements = Deplacement::whereIn('ppr', $userPprs)
            ->where('annee', $currentYear)
            ->whereHas('deplacementIns', function($query) use ($periodeId) {
                $query->where('deplacement_periode_id', $periodeId);
            })
            ->exists();
        
        if ($existingDeplacements) {
            return redirect()->back()->with('error', 'Des déplacements existent déjà pour cette période et cette année. Vous ne pouvez préparer qu\'une seule fois par trimestre par année.');
        }
        
        $months = $this->preparationService->getTrimestreMonths($periodeId);
        
        // Get users
        $users = User::whereHas('parcours', function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })
        ->with(['userInfo.grade.Echelle'])
        ->get();

        $agentsData = [];
        foreach ($users as $user) {
            $maxJours = $this->preparationService->getMaxJoursForUser($user, $periodeId);
            $agentData = [
                'user' => $user,
                'max_jours' => $maxJours,
                'months' => [],
            ];
            
            // Calculate total available days across all months
            $totalAvailable = 0;
            $monthAvailables = [];
            foreach ($months as $month) {
                $available = $this->preparationService->getAvailableDaysForMonth(
                    $user, 
                    $currentYear, 
                    $month, 
                    $periodeId
                );
                $monthAvailables[$month] = $available['available'];
                $totalAvailable += $available['available'];
            }
            
            // Auto-generate: distribute max_jours proportionally across months
            // But ensure each month doesn't exceed its available days
            $totalToDistribute = min($maxJours, $totalAvailable);
            
            // Initialize all months with 0
            foreach ($months as $month) {
                $agentData['months'][$month] = [
                    'available' => $monthAvailables[$month],
                    'suggested' => 0,
                ];
            }
            
            // Distribute days proportionally
            if ($totalAvailable > 0 && $totalToDistribute > 0) {
                $distributed = 0;
                $monthCount = count($months);
                
                // First pass: proportional distribution
                foreach ($months as $index => $month) {
                    $available = $monthAvailables[$month];
                    $remainingToDistribute = $totalToDistribute - $distributed;
                    $remainingMonths = $monthCount - $index;
                    
                    if ($remainingMonths > 0) {
                        // Proportional share, but ensure we don't exceed available or remaining budget
                        $proportional = $totalAvailable > 0 
                            ? round(($available / $totalAvailable) * $totalToDistribute)
                            : 0;
                        
                        // Take the minimum of: proportional, available, remaining budget, or equal distribution
                        $suggested = min($proportional, $available, $remainingToDistribute);
                        
                        // For last month, use all remaining
                        if ($index === $monthCount - 1) {
                            $suggested = min($available, $remainingToDistribute);
                        }
                        
                        $agentData['months'][$month]['suggested'] = max(0, $suggested);
                        $distributed += $suggested;
                    }
                }
                
                // Second pass: distribute any remaining days
                $currentTotal = array_sum(array_column($agentData['months'], 'suggested'));
                $remaining = $totalToDistribute - $currentTotal;
                
                if ($remaining > 0) {
                    foreach ($months as $month) {
                        if ($remaining <= 0) break;
                        $current = $agentData['months'][$month]['suggested'];
                        $available = $agentData['months'][$month]['available'];
                        $canAdd = min($remaining, $available - $current);
                        $agentData['months'][$month]['suggested'] += $canAdd;
                        $remaining -= $canAdd;
                    }
                }
            }
            
            $agentsData[] = $agentData;
        }

        return view('deplacements.process-step1', compact(
            'type', 
            'periode', 
            'entite', 
            'agentsData', 
            'months',
            'currentYear'
        ));
    }

    /**
     * Process Step 1 - Save days per month.
     */
    public function processStep1(Request $request, $type, $periodeId, $entiteId)
    {
        $user = auth()->user();
        $entite = Entite::findOrFail($entiteId);
        
        // Only chefs (not admins) can access step 1
        if ($entite->chef_ppr !== $user->ppr) {
            abort(403, 'Unauthorized.');
        }
        
        // Prevent admins from accessing step 1
        if ($user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Les administrateurs ne peuvent pas accéder à cette étape.');
        }

        $validated = $request->validate([
            'total_days' => 'required|array',
            'total_days.*' => 'required|integer|min:0',
            'days' => 'required|array',
            'days.*' => 'required|array',
            'days.*.*' => 'required|integer|min:0',
        ]);

        // Validate that sum of days doesn't exceed total_days for each user
        foreach ($validated['days'] as $ppr => $months) {
            $totalDays = $validated['total_days'][$ppr] ?? 0;
            $sumMonths = array_sum($months);
            
            if ($sumMonths > $totalDays) {
                return redirect()->back()
                    ->withErrors(['days' => "Le total des mois pour l'agent {$ppr} ({$sumMonths}) dépasse le nombre de jours défini ({$totalDays})."])
                    ->withInput();
            }
        }

        // Store in session for step 2
        session([
            'deplacement_step1_data' => $validated['days'],
            'deplacement_total_days' => $validated['total_days']
        ]);

        return redirect()->route('deplacements.process-step2', [
            'type' => $type,
            'periode' => $periodeId,
            'entite' => $entiteId
        ]);
    }

    /**
     * Step 2: Choose dates.
     */
    public function processStep2($type, $periodeId, $entiteId)
    {
        $user = auth()->user();
        $entite = Entite::findOrFail($entiteId);
        
        // Only chefs (not admins) can access step 2
        if ($entite->chef_ppr !== $user->ppr) {
            abort(403, 'Unauthorized.');
        }
        
        // Prevent admins from accessing step 2
        if ($user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Les administrateurs ne peuvent pas accéder à cette étape.');
        }

        $step1Data = session('deplacement_step1_data');
        if (!$step1Data) {
            return redirect()->route('deplacements.start-process', [
                'type' => $type,
                'periode' => $periodeId,
                'entite' => $entiteId
            ])->with('error', 'Veuillez d\'abord compléter l\'étape 1.');
        }

        $periode = DeplacementPeriode::findOrFail($periodeId);
        $currentYear = now()->year;
        $months = $this->preparationService->getTrimestreMonths($periodeId);
        
        // Get users
        $users = User::whereHas('parcours', function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })
        ->get();

        $agentsData = [];
        foreach ($users as $user) {
            $ppr = $user->ppr;
            if (!isset($step1Data[$ppr])) {
                continue;
            }
            
            $agentData = [
                'user' => $user,
                'months' => [],
            ];
            
            foreach ($months as $month) {
                $days = $step1Data[$ppr][$month] ?? 0;
                if ($days > 0) {
                    $blockedDates = $this->preparationService->getBlockedDatesForMonth($user, $currentYear, $month);
                    $agentData['months'][$month] = [
                        'days' => $days,
                        'blocked_dates' => $blockedDates,
                    ];
                }
            }
            
            $agentsData[] = $agentData;
        }

        return view('deplacements.process-step2', compact(
            'type', 
            'periode', 
            'entite', 
            'agentsData', 
            'months',
            'currentYear'
        ));
    }

    /**
     * Finalize process - Save deplacements.
     */
    public function finalizeProcess(Request $request, $type, $periodeId, $entiteId)
    {
        $user = auth()->user();
        $entite = Entite::findOrFail($entiteId);
        
        // Only chefs (not admins) can finalize
        if ($entite->chef_ppr !== $user->ppr) {
            abort(403, 'Unauthorized.');
        }
        
        // Prevent admins from finalizing
        if ($user->hasAnyRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh'])) {
            abort(403, 'Les administrateurs ne peuvent pas finaliser les déplacements.');
        }
        
        $currentYear = now()->year;
        
        // Check if deplacements already exist for this periode, year, and entite
        $userPprs = User::whereHas('parcours', function($query) use ($entiteId) {
            $query->where('entite_id', $entiteId)
                  ->where(function($q) {
                      $q->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                  });
        })->pluck('ppr');
        
        $existingDeplacements = Deplacement::whereIn('ppr', $userPprs)
            ->where('annee', $currentYear)
            ->whereHas('deplacementIns', function($query) use ($periodeId) {
                $query->where('deplacement_periode_id', $periodeId);
            })
            ->exists();
        
        if ($existingDeplacements) {
            return redirect()->back()->with('error', 'Des déplacements existent déjà pour cette période et cette année. Vous ne pouvez préparer qu\'une seule fois par trimestre par année.');
        }

        // Custom validation - only validate date_debut, not date_debut_display
        $request->validate([
            'dates' => 'required|array',
            'dates.*' => 'required|array',
        ]);
        
        // Validate date_debut specifically
        foreach ($request->input('dates', []) as $ppr => $userDates) {
            foreach ($userDates as $month => $dateData) {
                if (isset($dateData['date_debut'])) {
                    $request->validate([
                        "dates.{$ppr}.{$month}.date_debut" => 'required|date',
                    ]);
                }
            }
        }

        $step1Data = session('deplacement_step1_data');
        if (!$step1Data) {
            return redirect()->back()->with('error', 'Données de l\'étape 1 manquantes.');
        }

        $months = $this->preparationService->getTrimestreMonths($periodeId);

        try {
            DB::transaction(function() use ($request, $step1Data, $periodeId, $currentYear, $months, $entiteId) {
                $dates = $request->input('dates', []);
                
                foreach ($dates as $ppr => $userDates) {
                    // Get user by PPR (not ID) with relationships
                    $user = User::where('ppr', $ppr)
                        ->with(['userInfo.grade.Echelle'])
                        ->first();
                    if (!$user) {
                        continue;
                    }
                    
                    foreach ($months as $month) {
                        if (!isset($userDates[$month]['date_debut']) || !isset($step1Data[$ppr][$month])) {
                            continue;
                        }
                        
                        $dateDebut = Carbon::parse($userDates[$month]['date_debut']);
                        $nbrJours = (int) $step1Data[$ppr][$month];
                        
                        // Calculate date_fin based on working days (same logic as JavaScript)
                        $blockedDates = $this->preparationService->getBlockedDatesForMonth($user, $currentYear, $month);
                        $dateFin = $this->preparationService->calculateDateFin($dateDebut, $nbrJours, $blockedDates);
                        
                        // Validate dates
                        if (!$dateDebut || $nbrJours <= 0) {
                            continue;
                        }
                        
                        // Get user's echelle for tarif
                        $echelle = null;
                        if ($user->userInfo && $user->userInfo->grade && $user->userInfo->grade->Echelle) {
                            $echelle = $user->userInfo->grade->Echelle;
                        }
                        
                        // Get tarif for this echelle
                        $tarif = null;
                        $montantDeplacement = 0;
                        if ($echelle) {
                            $tarif = \App\Models\EchelleTarif::where('echelle_id', $echelle->id)
                                ->where('type_in_out_mission', 'in')
                                ->first();
                            
                            if ($tarif && $tarif->montant_deplacement) {
                                $montantDeplacement = (float) $tarif->montant_deplacement;
                            }
                        }
                        
                        // Calculate somme: nombre de jours * montant_deplacement
                        $somme = $montantDeplacement * $nbrJours;
                        
                        // Create deplacement
                        $deplacement = Deplacement::create([
                            'ppr' => $ppr,
                            'date_debut' => $dateDebut,
                            'date_fin' => $dateFin,
                            'nbr_jours' => $nbrJours,
                            'echelle_tarifs_id' => $tarif?->id,
                            'somme' => $somme,
                            'annee' => $currentYear,
                            'type_in_out' => 'in',
                        ]);
                        
                        // Create deplacement_in
                        $monthNames = [
                            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                        ];
                        
                        DeplacementIn::create([
                            'deplacement_id' => $deplacement->id,
                            'objet' => 'Déplacement trimestriel',
                            'mois' => $monthNames[$month] ?? Carbon::create($currentYear, $month, 1)->format('F'),
                            'deplacement_periode_id' => $periodeId,
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Error finalizing deplacement process: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création des déplacements: ' . $e->getMessage());
        }

        session()->forget('deplacement_step1_data');

        return redirect()->route('deplacements.by-entity', [
            'type' => $type,
            'periode' => $periodeId,
            'entite' => $entiteId
        ])->with('success', 'Déplacements créés avec succès.');
    }

    /**
     * Show deplacements statistics (admin only)
     */
    public function stats()
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        // Total deplacements
        $totalDeplacements = Deplacement::count();
        
        // Deplacements by type (in/out)
        $deplacementsByType = DB::table('deplacements')
            ->select('type_in_out', DB::raw('count(*) as count'))
            ->groupBy('type_in_out')
            ->get();

        // Deplacements by year
        $deplacementsByYear = DB::table('deplacements')
            ->select('annee', DB::raw('count(*) as count'))
            ->whereNotNull('annee')
            ->groupBy('annee')
            ->orderBy('annee', 'desc')
            ->get();

        // Monthly trends (last 6 months)
        $monthlyDeplacements = [];
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthlyDeplacements[] = Deplacement::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Recent deplacements (last 10)
        $recentDeplacements = Deplacement::with(['user', 'echelleTarif.echelle'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // This year statistics
        $deplacementsThisYear = Deplacement::where('annee', now()->year)->count();
        $deplacementsThisMonth = Deplacement::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Total amount statistics
        $totalAmount = Deplacement::sum('somme') ?? 0;
        $amountThisYear = Deplacement::where('annee', now()->year)->sum('somme') ?? 0;
        $amountThisMonth = Deplacement::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('somme') ?? 0;

        // Statistics by entity type (Central vs Regional)
        $centralEntiteIds = Entite::where('type', 'Central')->pluck('id');
        $centralUserPprs = Parcours::whereIn('entite_id', $centralEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->distinct()
            ->pluck('ppr');
        
        $centralDeplacements = Deplacement::whereIn('ppr', $centralUserPprs)->count();
        
        $regionalEntiteIds = Entite::where('type', 'Régional')->pluck('id');
        $regionalUserPprs = Parcours::whereIn('entite_id', $regionalEntiteIds)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->distinct()
            ->pluck('ppr');
        
        $regionalDeplacements = Deplacement::whereIn('ppr', $regionalUserPprs)->count();

        // Average days per deplacement
        $avgDays = Deplacement::where('nbr_jours', '>', 0)->avg('nbr_jours') ?? 0;
        $totalDays = Deplacement::sum('nbr_jours') ?? 0;

        return view('deplacements.stats', compact(
            'totalDeplacements',
            'deplacementsByType',
            'deplacementsByYear',
            'monthlyDeplacements',
            'months',
            'recentDeplacements',
            'deplacementsThisYear',
            'deplacementsThisMonth',
            'totalAmount',
            'amountThisYear',
            'amountThisMonth',
            'centralDeplacements',
            'regionalDeplacements',
            'avgDays',
            'totalDays'
        ));
    }
}


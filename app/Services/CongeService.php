<?php

namespace App\Services;

use App\Models\Conge;
use App\Models\Demande;
use App\Repositories\CongeRepository;
use Carbon\Carbon;

class CongeService
{
    protected CongeRepository $congeRepository;

    public function __construct(CongeRepository $congeRepository)
    {
        $this->congeRepository = $congeRepository;
    }

    /**
     * Create a new conge.
     */
    public function create(array $data): Conge
    {
        return $this->congeRepository->create($data);
    }

    /**
     * Update a conge.
     */
    public function update(Conge $conge, array $data): Conge
    {
        $this->congeRepository->update($conge->id, $data);
        return $conge->fresh();
    }

    /**
     * Delete a conge.
     */
    public function delete(Conge $conge): bool
    {
        return $this->congeRepository->delete($conge->id);
    }

    /**
     * Get or create annual leave record for a user and year.
     */
    public function getOrCreateAnnualLeave(string $ppr, int $year): Conge
    {
        return Conge::firstOrCreate(
            [
                'ppr' => $ppr,
                'annee' => $year,
            ],
            [
                'reference_decision' => null,
                'reliquat_annee_anterieure' => 0,
                'reliquat_annee_courante' => 0,
                'cumul_jours_consommes' => 0,
            ]
        );
    }

    /**
     * Get solde conge from solde_conges table.
     */
    public function getSoldeConge(string $ppr, int $year): ?\stdClass
    {
        return \Illuminate\Support\Facades\DB::table('solde_conges')
            ->where('ppr', $ppr)
            ->where('type', 'Congé Administratif Annuel')
            ->where('annee', $year)
            ->first();
    }

    /**
     * Calculate remaining leave days for a conge.
     */
    public function calculateRemainingDays(Conge $conge): int
    {
        $totalAvailable = $conge->reliquat_annee_anterieure + $conge->reliquat_annee_courante;
        return max(0, $totalAvailable - $conge->cumul_jours_consommes);
    }

    /**
     * Calculate annual leave balance for a user.
     * Uses solde_conges table if available, otherwise falls back to conges table.
     */
    public function calculateAnnualBalance(string $ppr, ?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;
        
        // Try to get data from solde_conges table first
        $soldeConge = $this->getSoldeConge($ppr, $year);
        
        if ($soldeConge) {
            // Map solde_conges fields to the expected format
            $reliquatAnneeAnterieure = $soldeConge->solde_precedent ?? 0;
            $reliquatAnneeCourante = $soldeConge->solde_fix ?? 0;
            $soldeActuel = $soldeConge->solde_actuel ?? 0;
            
            // Calculate consumed days: (previous + current) - actual balance
            $totalAvailable = $reliquatAnneeAnterieure + $reliquatAnneeCourante;
            $cumulJoursConsommes = max(0, $totalAvailable - $soldeActuel);
            
            // Get actual days consumed from approved avis de retour (use actual days, not requested days)
            $approvedAvisRetourDays = \App\Models\Demande::where('ppr', $ppr)
                ->whereHas('avis.avisRetour', function($query) {
                    $query->where('statut', 'approved');
                })
                ->with(['avis.avisRetour'])
                ->get()
                ->sum(function($demande) {
                    $avisRetour = $demande->avis->avisRetour ?? null;
                    if ($avisRetour && $avisRetour->statut === 'approved') {
                        // Use actual days consumed from avis de retour
                        return $avisRetour->nbr_jours_consumes ?? 0;
                    }
                    return 0;
                });
            
            // Get approved avis de départ days that don't have avis de retour yet (still pending return)
            $approvedAvisDepartWithoutRetourDays = \App\Models\Demande::where('ppr', $ppr)
                ->whereHas('avis.avisDepart', function($query) {
                    $query->where('statut', 'approved');
                })
                ->whereHas('avis', function($query) {
                    $query->whereDoesntHave('avisRetour');
                })
                ->with(['avis.avisDepart'])
                ->get()
                ->sum(function($demande) {
                    $avisDepart = $demande->avis->avisDepart ?? null;
                    return $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0;
                });
            
            // Deduct pending avis de départ days from remaining balance
            $pendingAvisDepartDays = \App\Models\Demande::where('ppr', $ppr)
                ->whereHas('avis.avisDepart', function($query) {
                    $query->where('statut', 'pending');
                })
                ->with(['avis.avisDepart'])
                ->get()
                ->sum(function($demande) {
                    $avisDepart = $demande->avis->avisDepart ?? null;
                    return $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0;
                });
            
            // Calculate remaining days: solde_actuel minus actual consumed days and pending days
            $reste = max(0, $soldeActuel - $approvedAvisRetourDays - $approvedAvisDepartWithoutRetourDays - $pendingAvisDepartDays);
            
            // Total consumed = base consumed + actual avis retour days + approved avis départ without retour + pending days
            $totalConsumed = $cumulJoursConsommes + $approvedAvisRetourDays + $approvedAvisDepartWithoutRetourDays + $pendingAvisDepartDays;
            
            return [
                'conge' => null, // No Conge model instance when using solde_conges
                'reference_decision' => 'N/A',
                'reliquat_annee_anterieure' => $reliquatAnneeAnterieure,
                'reliquat_annee_courante' => $reliquatAnneeCourante,
                'cumul_jours_consommes' => $totalConsumed,
                'reste' => $reste,
                'jours_restants' => $reste,
                'has_remaining_leave' => $reste > 0,
            ];
        }
        
        // Fallback to conges table if solde_conges doesn't have data
        $conge = $this->getOrCreateAnnualLeave($ppr, $year);
        
        // Get actual days consumed from approved avis de retour (use actual days, not requested days)
        $approvedAvisRetourDays = \App\Models\Demande::where('ppr', $ppr)
            ->whereHas('avis.avisRetour', function($query) {
                $query->where('statut', 'approved');
            })
            ->with(['avis.avisRetour'])
            ->get()
            ->sum(function($demande) {
                $avisRetour = $demande->avis->avisRetour ?? null;
                if ($avisRetour && $avisRetour->statut === 'approved') {
                    // Use actual days consumed from avis de retour
                    return $avisRetour->nbr_jours_consumes ?? 0;
                }
                return 0;
            });
        
        // Get approved avis de départ days that don't have avis de retour yet (still pending return)
        $approvedAvisDepartWithoutRetourDays = \App\Models\Demande::where('ppr', $ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'approved');
            })
            ->whereHas('avis', function($query) {
                $query->whereDoesntHave('avisRetour');
            })
            ->with(['avis.avisDepart'])
            ->get()
            ->sum(function($demande) {
                $avisDepart = $demande->avis->avisDepart ?? null;
                return $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0;
            });
        
        // Deduct pending avis de départ days from remaining balance
        $pendingAvisDepartDays = \App\Models\Demande::where('ppr', $ppr)
            ->whereHas('avis.avisDepart', function($query) {
                $query->where('statut', 'pending');
            })
            ->with(['avis.avisDepart'])
            ->get()
            ->sum(function($demande) {
                $avisDepart = $demande->avis->avisDepart ?? null;
                return $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0;
            });
        
        // Calculate remaining days: base remaining minus actual consumed and pending days
        $baseRemaining = $this->calculateRemainingDays($conge);
        $reste = max(0, $baseRemaining - $approvedAvisRetourDays - $approvedAvisDepartWithoutRetourDays - $pendingAvisDepartDays);
        
        // Total consumed = base consumed + actual avis retour days + approved avis départ without retour + pending days
        $totalConsumed = $conge->cumul_jours_consommes + $approvedAvisRetourDays + $approvedAvisDepartWithoutRetourDays + $pendingAvisDepartDays;

        return [
            'conge' => $conge,
            'reference_decision' => $conge->reference_decision ?? 'N/A',
            'reliquat_annee_anterieure' => $conge->reliquat_annee_anterieure,
            'reliquat_annee_courante' => $conge->reliquat_annee_courante,
            'cumul_jours_consommes' => $totalConsumed,
            'reste' => $reste,
            'jours_restants' => $reste,
            'has_remaining_leave' => $reste > 0,
        ];
    }

    /**
     * Refund days to conge balance.
     */
    public function refundDays(string $ppr, int $nbJours, ?int $year = null): void
    {
        $year = $year ?? Carbon::now()->year;
        $conge = Conge::where('ppr', $ppr)
            ->where('annee', $year)
            ->first();
        
        if ($conge && $conge->cumul_jours_consommes >= $nbJours) {
            $conge->decrement('cumul_jours_consommes', $nbJours);
        }
    }

    /**
     * Update solde_conges based on actual days consumed from avis de retour.
     * This adjusts the balance when avis de retour is validated.
     */
    public function updateSoldeFromAvisRetour(string $ppr, int $actualDaysConsumed, int $previouslyDeductedDays, ?int $year = null): void
    {
        $year = $year ?? Carbon::now()->year;
        
        // Get solde_conges record
        $soldeConge = \Illuminate\Support\Facades\DB::table('solde_conges')
            ->where('ppr', $ppr)
            ->where('type', 'Congé Administratif Annuel')
            ->where('annee', $year)
            ->first();
        
        if (!$soldeConge) {
            return; // No solde record to update
        }
        
        // Calculate the difference
        // If actualDaysConsumed < previouslyDeductedDays, difference is negative (we refund)
        // If actualDaysConsumed > previouslyDeductedDays, difference is positive (we deduct more)
        $difference = $actualDaysConsumed - $previouslyDeductedDays;
        
        if ($difference == 0) {
            // No adjustment needed (actual days = previously deducted)
            return;
        }
        
        // Special case: if actualDaysConsumed is 0 (same day return), we refund all previously deducted days
        // This ensures same-day returns result in full refund
        
        // Update solde_actuel
        // If difference is negative (e.g., -3), we're refunding 3 days, so add 3: solde - (-3) = solde + 3
        // If difference is positive (e.g., +1), we're deducting 1 more day, so subtract 1: solde - 1
        $newSoldeActuel = $soldeConge->solde_actuel - $difference;
        
        // Ensure solde_actuel doesn't exceed total available
        $totalAvailable = ($soldeConge->solde_precedent ?? 0) + ($soldeConge->solde_fix ?? 0);
        $newSoldeActuel = min($newSoldeActuel, $totalAvailable);
        $newSoldeActuel = max(0, $newSoldeActuel); // Can't be negative
        
        \Illuminate\Support\Facades\DB::table('solde_conges')
            ->where('ppr', $ppr)
            ->where('type', 'Congé Administratif Annuel')
            ->where('annee', $year)
            ->update([
                'solde_actuel' => $newSoldeActuel,
                'updated_at' => Carbon::now(),
            ]);
    }
}



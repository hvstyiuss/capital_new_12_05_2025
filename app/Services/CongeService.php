<?php

namespace App\Services;

use App\Models\Conge;
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
            
            // Use solde_actuel as remaining days
            $reste = $soldeActuel;
            
            return [
                'conge' => null, // No Conge model instance when using solde_conges
                'reference_decision' => 'N/A',
                'reliquat_annee_anterieure' => $reliquatAnneeAnterieure,
                'reliquat_annee_courante' => $reliquatAnneeCourante,
                'cumul_jours_consommes' => $cumulJoursConsommes,
                'reste' => $reste,
                'jours_restants' => $reste,
                'has_remaining_leave' => $reste > 0,
            ];
        }
        
        // Fallback to conges table if solde_conges doesn't have data
        $conge = $this->getOrCreateAnnualLeave($ppr, $year);
        $reste = $this->calculateRemainingDays($conge);

        return [
            'conge' => $conge,
            'reference_decision' => $conge->reference_decision ?? 'N/A',
            'reliquat_annee_anterieure' => $conge->reliquat_annee_anterieure,
            'reliquat_annee_courante' => $conge->reliquat_annee_courante,
            'cumul_jours_consommes' => $conge->cumul_jours_consommes,
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
}



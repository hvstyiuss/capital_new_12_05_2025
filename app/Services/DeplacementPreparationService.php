<?php

namespace App\Services;

use App\Models\User;
use App\Models\Demande;
use App\Models\DemandeConge;
use App\Models\JoursFerie;
use App\Models\HorsBareme;
use App\Models\EchelleTarif;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DeplacementPreparationService
{
    /**
     * Get available days for a user in a month.
     */
    public function getAvailableDaysForMonth(User $user, int $year, int $month, int $periodeId): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $totalDays = $endDate->day;
        $weekends = $this->countWeekends($startDate, $endDate);
        $joursFeries = $this->countJoursFeries($startDate, $endDate);
        $conges = $this->countConges($user, $startDate, $endDate);
        
        $availableDays = $totalDays - $weekends - $joursFeries - $conges;
        
        return [
            'total' => $totalDays,
            'weekends' => $weekends,
            'jours_feries' => $joursFeries,
            'conges' => $conges,
            'available' => max(0, $availableDays),
        ];
    }

    /**
     * Get conges for a user in a date range.
     */
    public function getCongesForPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        return Demande::where('ppr', $user->ppr)
            ->where('type', 'conge')
            ->where('statut', 'approved')
            ->whereHas('demandeConge', function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date_debut', [$startDate, $endDate])
                      ->orWhereBetween('date_fin', [$startDate, $endDate])
                      ->orWhere(function($sub) use ($startDate, $endDate) {
                          $sub->where('date_debut', '<=', $startDate)
                              ->where('date_fin', '>=', $endDate);
                      });
                });
            })
            ->with('demandeConge')
            ->get();
    }

    /**
     * Get max jours for a user based on echelle_tarifs or hors_bareme.
     */
    public function getMaxJoursForUser(User $user, int $periodeId): int
    {
        // Check if user is in hors_bareme
        $horsBareme = HorsBareme::where('ppr', $user->ppr)
            ->where('deplacement_periode_id', $periodeId)
            ->first();
        
        if ($horsBareme) {
            return $horsBareme->nb_jours;
        }
        
        // Get from echelle_tarifs
        $echelle = $user->userInfo->grade->Echelle ?? null;
        if (!$echelle) {
            return 9; // Default
        }
        
        $tarif = EchelleTarif::where('echelle_id', $echelle->id)
            ->where('type_in_out_mission', 'in')
            ->first();
        
        return $tarif ? ($tarif->max_jours ?? 9) : 9;
    }

    /**
     * Get blocked dates for a user in a month (conges + jours feries + weekends).
     */
    public function getBlockedDatesForMonth(User $user, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $blockedDates = [];
        
        // Get weekends
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->isWeekend()) {
                $blockedDates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }
        
        // Get jours feries
        $joursFeries = JoursFerie::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();
        
        $blockedDates = array_merge($blockedDates, $joursFeries);
        
        // Get conges
        $conges = $this->getCongesForPeriod($user, $startDate, $endDate);
        foreach ($conges as $demande) {
            $conge = $demande->demandeConge;
            if ($conge && $conge->date_debut && $conge->date_fin) {
                $current = Carbon::parse($conge->date_debut);
                $end = Carbon::parse($conge->date_fin);
                while ($current <= $end && $current <= $endDate && $current >= $startDate) {
                    $blockedDates[] = $current->format('Y-m-d');
                    $current->addDay();
                }
            }
        }
        
        return array_unique($blockedDates);
    }

    /**
     * Count weekends in a date range.
     */
    private function countWeekends(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();
        while ($current <= $end) {
            if ($current->isWeekend()) {
                $count++;
            }
            $current->addDay();
        }
        return $count;
    }

    /**
     * Count jours feries in a date range.
     */
    private function countJoursFeries(Carbon $start, Carbon $end): int
    {
        return JoursFerie::whereBetween('date', [$start, $end])->count();
    }

    /**
     * Count conge days in a date range.
     */
    private function countConges(User $user, Carbon $start, Carbon $end): int
    {
        $conges = $this->getCongesForPeriod($user, $start, $end);
        $count = 0;
        
        foreach ($conges as $demande) {
            $conge = $demande->demandeConge;
            if ($conge && $conge->date_debut && $conge->date_fin) {
                $congeStart = Carbon::parse($conge->date_debut);
                $congeEnd = Carbon::parse($conge->date_fin);
                
                // Calculate overlap
                $overlapStart = max($congeStart, $start);
                $overlapEnd = min($congeEnd, $end);
                
                if ($overlapStart <= $overlapEnd) {
                    $count += $overlapStart->diffInDays($overlapEnd) + 1;
                }
            }
        }
        
        return $count;
    }

    /**
     * Calculate date fin based on working days (excluding weekends and blocked dates).
     */
    public function calculateDateFin(Carbon $dateDebut, int $nbrJours, array $blockedDates): Carbon
    {
        if ($nbrJours <= 0) {
            return $dateDebut->copy();
        }
        
        $current = $dateDebut->copy();
        $count = 0;
        
        // Count working days (excluding weekends and blocked dates)
        while ($count < $nbrJours) {
            $dateStr = $current->format('Y-m-d');
            $isWeekend = $current->isWeekend();
            $isBlocked = in_array($dateStr, $blockedDates);
            
            if (!$isWeekend && !$isBlocked) {
                $count++;
            }
            
            if ($count < $nbrJours) {
                $current->addDay();
            }
        }
        
        return $current;
    }

    /**
     * Get trimestre months based on period.
     */
    public function getTrimestreMonths(int $periodeId): array
    {
        $periode = \App\Models\DeplacementPeriode::find($periodeId);
        if (!$periode) {
            return [];
        }
        
        // Map periods to months
        $periodMonths = [
            1 => [1, 2, 3],   // TR1: Jan, Feb, Mar
            2 => [4, 5, 6],   // TR2: Apr, May, Jun
            3 => [7, 8, 9],   // TR3: Jul, Aug, Sep
            4 => [10, 11],    // TR4: Oct, Nov (not Dec)
        ];
        
        // Extract number from periode name (handles both "TR1" and "Trimestre 1" formats)
        $periodeName = trim($periode->name);
        
        // Try to extract number from "TR1", "TR2", etc.
        if (preg_match('/TR\s*(\d+)/i', $periodeName, $matches)) {
            $periodeNumber = (int) $matches[1];
        }
        // Try to extract number from "Trimestre 1", "Trimestre 2", etc.
        elseif (preg_match('/trimestre\s*(\d+)/i', $periodeName, $matches)) {
            $periodeNumber = (int) $matches[1];
        }
        // Try to extract just the number at the end
        elseif (preg_match('/(\d+)/', $periodeName, $matches)) {
            $periodeNumber = (int) $matches[1];
        }
        else {
            return [];
        }
        
        return $periodMonths[$periodeNumber] ?? [];
    }
}


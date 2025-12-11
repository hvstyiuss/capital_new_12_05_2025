<?php

namespace App\Actions\AvisRetour;

use App\Models\AvisRetour;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\CongeService;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DomainException;

class ValidateAvisRetourAction
{
    protected NotificationService $notificationService;
    protected CongeService $congeService;

    public function __construct(NotificationService $notificationService, CongeService $congeService)
    {
        $this->notificationService = $notificationService;
        $this->congeService = $congeService;
    }

    public function execute(
        AvisRetour $avisRetour,
        User $validator,
        ?string $dateRetourEffectif,
        callable $isChefOfUserCallback,
        callable $generatePdfCallback = null,
        callable $generateExplanationPdfCallback = null
    ): AvisRetour {
        $demande = $avisRetour->avis->demande;
        $demandeUser = $demande->user;
        $isChef = $isChefOfUserCallback($validator, $demandeUser);
        
        if (!$isChef && !$validator->hasRole('admin')) {
            throw new DomainException('Vous n\'avez pas l\'autorisation de valider cet avis de retour.');
        }

        // Get avis and avisDepart before updating
        $avis = $avisRetour->avis;
        $avisDepart = $avis ? $avis->avisDepart : null;
        
        // Calculate actual days consumed based on date_retour_effectif
        $actualDaysConsumed = 0;
        $previouslyDeductedDays = 0;
        
        if ($avisDepart && $avisDepart->date_depart) {
            // Get the date_retour_effectif (use provided one or the declared one)
            $dateRetourEffectifToUse = $dateRetourEffectif ?? $avisRetour->date_retour_effectif ?? $avisRetour->date_retour_declaree;
            
            if ($dateRetourEffectifToUse) {
                $dateDepart = Carbon::parse($avisDepart->date_depart);
                $dateRetour = Carbon::parse($dateRetourEffectifToUse);
                
                // Calculate actual days consumed
                // If same day, 0 days consumed
                // If different days, calculate the difference
                if ($dateDepart->isSameDay($dateRetour)) {
                    $actualDaysConsumed = 0; // Same day = no days consumed
                } else {
                    // Calculate difference: if return is after departure, count the days
                    if ($dateRetour->greaterThan($dateDepart)) {
                        $actualDaysConsumed = $dateDepart->diffInDays($dateRetour) + 1; // +1 to include both days
                    } else {
                        $actualDaysConsumed = 0; // Return before departure shouldn't happen, but set to 0
                    }
                }
                
                // Get previously deducted days from avis de départ
                $previouslyDeductedDays = $avisDepart->nb_jours_demandes ?? 0;
            }
        }
        
        // Update date_retour_effectif and nbr_jours_consumes if provided
        $updateData = [
            'statut' => 'approved',
            'nbr_jours_consumes' => $actualDaysConsumed,
        ];
        if ($dateRetourEffectif) {
            $updateData['date_retour_effectif'] = $dateRetourEffectif;
        } elseif (!$avisRetour->date_retour_effectif && $avisRetour->date_retour_declaree) {
            // If no date_retour_effectif provided but date_retour_declaree exists, use it
            $updateData['date_retour_effectif'] = $avisRetour->date_retour_declaree;
        }
        $avisRetour->update($updateData);

        // Reload the model to get the latest data
        $avisRetour->refresh();
        
        // Update solde_conges based on actual days consumed
        // Update balance if we have previously deducted days (even if actual is 0, we need to refund)
        if ($previouslyDeductedDays > 0) {
            try {
                $this->congeService->updateSoldeFromAvisRetour(
                    $demandeUser->ppr,
                    $actualDaysConsumed, // Can be 0 for same-day returns
                    $previouslyDeductedDays
                );
            } catch (\Exception $e) {
                // Log error but don't fail validation
                \Log::error('Error updating solde from avis retour: ' . $e->getMessage(), [
                    'avis_retour_id' => $avisRetour->id,
                    'ppr' => $demandeUser->ppr,
                    'actual_days' => $actualDaysConsumed,
                    'previously_deducted' => $previouslyDeductedDays,
                ]);
            }
        }
        
        // Generate PDF for avis de retour if not already generated
        if ($demandeUser && $generatePdfCallback) {
            try {
                if (Schema::hasColumn('avis_retours', 'pdf_path')) {
                    if (!$avisRetour->pdf_path) {
                        $pdfPath = $generatePdfCallback($avisRetour, $demandeUser, $avisDepart);
                        if ($pdfPath) {
                            $avisRetour->update(['pdf_path' => $pdfPath]);
                        }
                    }
                } else {
                    // Column doesn't exist, generate PDF but don't save path
                    $generatePdfCallback($avisRetour, $demandeUser, $avisDepart);
                }
            } catch (\Exception $e) {
                // Log error but don't fail validation
                \Log::error('Error generating avis retour PDF during validation: ' . $e->getMessage(), [
                    'avis_retour_id' => $avisRetour->id,
                    'exception' => get_class($e),
                ]);
            }
        }

        // Generate explanation PDF if actual return date exceeds declared return date
        // (i.e., user returned later than declared)
        if ($avisRetour->date_retour_declaree && $avisRetour->date_retour_effectif) {
            $dateRetourDeclaree = Carbon::parse($avisRetour->date_retour_declaree);
            $dateRetourEffectif = Carbon::parse($avisRetour->date_retour_effectif);
            
            // Check if actual return date is later than declared return date
            if ($dateRetourEffectif->greaterThan($dateRetourDeclaree) && $generateExplanationPdfCallback) {
                try {
                    if (Schema::hasColumn('avis_retours', 'explanation_pdf_path')) {
                        if (!$avisRetour->explanation_pdf_path) {
                            $explanationPdfPath = $generateExplanationPdfCallback($avisRetour, $demandeUser, $avisDepart);
                            if ($explanationPdfPath) {
                                $avisRetour->update(['explanation_pdf_path' => $explanationPdfPath]);
                            }
                        }
                    } else {
                        // Column doesn't exist, generate PDF but don't save path
                        $generateExplanationPdfCallback($avisRetour, $demandeUser, $avisDepart);
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail validation
                    \Log::error('Error generating explanation PDF during validation: ' . $e->getMessage(), [
                        'avis_retour_id' => $avisRetour->id,
                        'exception' => get_class($e),
                    ]);
                }
            }
        }

        return $avisRetour->fresh();
    }
}





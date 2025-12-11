<?php

namespace App\Services;

use App\Models\AvisDepart;
use App\Models\AvisRetour;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class LeavePDFService
{
    /**
     * Generate PDF for avis de départ
     */
    public function generateAvisDepartPDF(AvisDepart $avisDepart, User $user): string
    {
        try {
            // Ensure user has necessary relationships loaded
            if (!$user->relationLoaded('userInfo')) {
                $user->load('userInfo.grade');
            }
            
            // Get demande from avis
            $avis = $avisDepart->avis;
            $demande = $avis ? $avis->demande : null;
            
            if (!$demande) {
                throw new \Exception('Demande introuvable pour cet avis de départ.');
            }
            
            // Generate verification code if it doesn't exist and column exists
            if (Schema::hasColumn('avis_departs', 'verification_code')) {
                if (!$avisDepart->verification_code) {
                    $avisDepart->verification_code = $this->generateVerificationCode();
                    $avisDepart->save();
                }
            }
            
            // Generate verification URL if verification code exists
            $verificationUrl = null;
            if (Schema::hasColumn('avis_departs', 'verification_code') && $avisDepart->verification_code) {
                $verificationUrl = url('/verification/resultat1.php?code_verification=' . $avisDepart->verification_code);
            }
            
            // Generate PDF from blade template
            $pdf = Pdf::loadView('leaves.avis-depart-pdf', [
                'avisDepart' => $avisDepart,
                'user' => $user,
                'demande' => $demande,
                'verificationUrl' => $verificationUrl,
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $filename = 'avis-depart-' . $avisDepart->id . '-' . time() . '.pdf';
            $path = 'pdfs/avis-depart/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('pdfs/avis-depart');

            // Generate PDF output
            $pdfOutput = $pdf->output();
            
            if (empty($pdfOutput)) {
                throw new \Exception('La génération du PDF a retourné un contenu vide.');
            }

            // Save PDF to storage
            $saved = Storage::disk('public')->put($path, $pdfOutput);
            
            if (!$saved) {
                throw new \Exception('Impossible d\'enregistrer le PDF dans le stockage.');
            }

            return $path;
        } catch (\Exception $e) {
            \Log::error('Error in generateAvisDepartPDF: ' . $e->getMessage(), [
                'avis_depart_id' => $avisDepart->id,
                'user_ppr' => $user->ppr ?? null,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate PDF for avis de retour
     */
    public function generateAvisRetourPDF(AvisRetour $avisRetour, User $user, ?AvisDepart $avisDepart = null): string
    {
        try {
            // Ensure user has necessary relationships loaded
            if (!$user->relationLoaded('userInfo')) {
                $user->load('userInfo.grade');
            }
            
            // Get demande from avis
            $avis = $avisRetour->avis;
            $demande = $avis ? $avis->demande : null;
            
            if (!$demande) {
                throw new \Exception('Demande introuvable pour cet avis de retour.');
            }
            
            // If avisDepart not provided, get it from avis
            if (!$avisDepart && $avis) {
                $avisDepart = $avis->avisDepart;
            }
            
            // Generate verification code if it doesn't exist and column exists
            if (Schema::hasColumn('avis_retours', 'verification_code')) {
                if (!$avisRetour->verification_code) {
                    $avisRetour->verification_code = $this->generateVerificationCode();
                    $avisRetour->save();
                }
            }
            
            // Generate verification URL if verification code exists
            $verificationUrl = null;
            if (Schema::hasColumn('avis_retours', 'verification_code') && $avisRetour->verification_code) {
                $verificationUrl = url('/verification/resultat1.php?code_verification=' . $avisRetour->verification_code);
            }
            
            // Generate PDF from blade template
            $pdf = Pdf::loadView('leaves.avis-retour-pdf', [
                'avisRetour' => $avisRetour,
                'user' => $user,
                'avisDepart' => $avisDepart,
                'demande' => $demande,
                'verificationUrl' => $verificationUrl,
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $filename = 'avis-retour-' . $avisRetour->id . '-' . time() . '.pdf';
            $path = 'pdfs/avis-retour/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('pdfs/avis-retour');

            // Generate PDF output
            $pdfOutput = $pdf->output();
            
            if (empty($pdfOutput)) {
                throw new \Exception('La génération du PDF a retourné un contenu vide.');
            }

            // Save PDF to storage
            $saved = Storage::disk('public')->put($path, $pdfOutput);
            
            if (!$saved) {
                throw new \Exception('Impossible d\'enregistrer le PDF dans le stockage.');
            }

            return $path;
        } catch (\Exception $e) {
            \Log::error('Error in generateAvisRetourPDF: ' . $e->getMessage(), [
                'avis_retour_id' => $avisRetour->id,
                'user_ppr' => $user->ppr ?? null,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate a unique verification code
     */
    private function generateVerificationCode(): string
    {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            $code = (string) rand(100000000000, 999999999999); // 12-digit code
            $attempts++;
            
            // Check if code exists only if columns exist
            $exists = false;
            if (Schema::hasColumn('avis_departs', 'verification_code')) {
                $exists = $exists || \App\Models\AvisDepart::where('verification_code', $code)->exists();
            }
            if (Schema::hasColumn('avis_retours', 'verification_code')) {
                $exists = $exists || \App\Models\AvisRetour::where('verification_code', $code)->exists();
            }
            
            if ($attempts >= $maxAttempts) {
                // Fallback: use timestamp-based code if we can't find a unique random one
                $code = (string) (time() * 1000 + rand(0, 999));
                break;
            }
        } while ($exists);
        
        return $code;
    }

    /**
     * Generate explanation PDF for avis de retour
     */
    public function generateExplanationPDF(AvisRetour $avisRetour, User $user, ?AvisDepart $avisDepart = null): string
    {
        // Get demande from avis
        $avis = $avisRetour->avis;
        $demande = $avis ? $avis->demande : null;
        
        // If avisDepart not provided, get it from avis
        if (!$avisDepart && $avis) {
            $avisDepart = $avis->avisDepart;
        }
        
        // Generate PDF from blade template
        $pdf = Pdf::loadView('leaves.explanation-pdf', [
            'avisRetour' => $avisRetour,
            'user' => $user,
            'avisDepart' => $avisDepart,
            'demande' => $demande,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = 'explication-retard-' . $avisRetour->id . '-' . time() . '.pdf';
        $path = 'pdfs/explanations/' . $filename;

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('pdfs/explanations');

        // Save PDF to storage
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}


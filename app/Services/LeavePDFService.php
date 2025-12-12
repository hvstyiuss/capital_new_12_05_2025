<?php

namespace App\Services;

use App\Models\AvisDepart;
use App\Models\AvisRetour;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LeavePDFService
{
    /**
     * Generate PDF for avis de départ (returns PDF object, does not save)
     */
    public function generateAvisDepartPDF(AvisDepart $avisDepart, User $user)
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
                $verificationUrl = url('/verification/avis/' . $avisDepart->verification_code);
            }
            
            // Prepare user data for PDF
            $userData = $this->prepareUserDataForPDF($user);
            
            // Prepare QR code data
            $qrCodeData = $this->prepareQRCodeData($verificationUrl);
            
            // Prepare formatted dates
            $dateDepartFormatted = $avisDepart->date_depart 
                ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') 
                : '';
            $dateRetourFormatted = $avisDepart->date_retour 
                ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d-m-Y') 
                : '';
            
            // Generate PDF using DomPDF
            $pdf = Pdf::loadView('leaves.avis-depart-pdf', [
                'avisDepart' => $avisDepart,
                'user' => $user,
                'demande' => $demande,
                'verificationUrl' => $verificationUrl,
                'currentParcours' => $userData['currentParcours'],
                'gradeName' => $userData['gradeName'],
                'serviceName' => $userData['serviceName'],
                'parentEntityName' => $userData['parentEntityName'],
                'logoData' => $userData['logoData'],
                'qrCodeData' => $qrCodeData,
                'dateDepartFormatted' => $dateDepartFormatted,
                'dateRetourFormatted' => $dateRetourFormatted,
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            return $pdf;
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
     * Generate PDF for avis de retour (returns PDF object, does not save)
     */
    public function generateAvisRetourPDF(AvisRetour $avisRetour, User $user, ?AvisDepart $avisDepart = null)
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
                $verificationUrl = url('/verification/avis/' . $avisRetour->verification_code);
            }
            
            // Prepare user data for PDF
            $userData = $this->prepareUserDataForPDF($user);
            
            // Prepare QR code data
            $qrCodeData = $this->prepareQRCodeData($verificationUrl);
            
            // Prepare formatted dates
            $dateDepartFormatted = $avisDepart && $avisDepart->date_depart 
                ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') 
                : '';
            $dateRetourDeclareeFormatted = $avisRetour->date_retour_declaree 
                ? \Carbon\Carbon::parse($avisRetour->date_retour_declaree)->format('d-m-Y') 
                : '';
            $dateRetourEffectifFormatted = $avisRetour->date_retour_effectif 
                ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('d-m-Y') 
                : '';
            
            // Generate PDF using DomPDF
            $pdf = Pdf::loadView('leaves.avis-retour-pdf', [
                'avisRetour' => $avisRetour,
                'user' => $user,
                'avisDepart' => $avisDepart,
                'demande' => $demande,
                'verificationUrl' => $verificationUrl,
                'currentParcours' => $userData['currentParcours'],
                'gradeName' => $userData['gradeName'],
                'serviceName' => $userData['serviceName'],
                'parentEntityName' => $userData['parentEntityName'],
                'logoData' => $userData['logoData'],
                'qrCodeData' => $qrCodeData,
                'dateDepartFormatted' => $dateDepartFormatted,
                'dateRetourDeclareeFormatted' => $dateRetourDeclareeFormatted,
                'dateRetourEffectifFormatted' => $dateRetourEffectifFormatted,
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            return $pdf;
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
     * Generate explanation PDF for avis de retour (returns PDF object, does not save)
     */
    public function generateExplanationPDF(AvisRetour $avisRetour, User $user, ?AvisDepart $avisDepart = null)
    {
        // Get demande from avis
        $avis = $avisRetour->avis;
        $demande = $avis ? $avis->demande : null;
        
        // If avisDepart not provided, get it from avis
        if (!$avisDepart && $avis) {
            $avisDepart = $avis->avisDepart;
        }
        
        // Calculate explanation data
        $explanationData = $this->calculateExplanationData($avisRetour, $avisDepart);
        
        // Calculate deadline (48 hours from now)
        $deadline = \Carbon\Carbon::now()->addHours(48);
        
        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('leaves.explanation-pdf', [
            'avisRetour' => $avisRetour,
            'user' => $user,
            'avisDepart' => $avisDepart,
            'demande' => $demande,
            'deadline' => $deadline,
            'explanationData' => $explanationData,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Calculate explanation data for PDF
     */
    private function calculateExplanationData(AvisRetour $avisRetour, ?AvisDepart $avisDepart): array
    {
        if (!$avisDepart || !$avisRetour->date_retour_declaree) {
            return [
                'isLateReturn' => false,
                'isConsumptionExceeded' => false,
                'expectedDays' => 0,
                'dateRetourPrevue' => null,
                'dateRetourDeclaree' => null,
                'dateDepart' => null,
            ];
        }
        
        $dateRetourPrevue = \Carbon\Carbon::parse($avisDepart->date_retour);
        $dateRetourDeclaree = \Carbon\Carbon::parse($avisRetour->date_retour_declaree);
        $dateDepart = \Carbon\Carbon::parse($avisDepart->date_depart);
        
        // Calculate expected days from departure to declared return date
        $current = $dateDepart->copy();
        $expectedDays = 0;
        $holidays = \App\Models\JoursFerie::whereBetween('date', [$dateDepart, $dateRetourDeclaree])
            ->pluck('date')
            ->map(function($date) {
                return $date->format('Y-m-d');
            })
            ->toArray();
        
        while ($current->lte($dateRetourDeclaree)) {
            $dateString = $current->format('Y-m-d');
            if (!$current->isWeekend() && !in_array($dateString, $holidays)) {
                $expectedDays++;
            }
            $current->addDay();
        }
        
        $isLateReturn = $dateRetourDeclaree->gt($dateRetourPrevue);
        $isConsumptionExceeded = $avisRetour->nbr_jours_consumes > $expectedDays;
        
        return [
            'isLateReturn' => $isLateReturn,
            'isConsumptionExceeded' => $isConsumptionExceeded,
            'expectedDays' => $expectedDays,
            'dateRetourPrevue' => $dateRetourPrevue,
            'dateRetourDeclaree' => $dateRetourDeclaree,
            'dateDepart' => $dateDepart,
        ];
    }

    /**
     * Prepare user data for PDF generation
     */
    private function prepareUserDataForPDF(User $user): array
    {
        // Get current parcours
        $currentParcours = \App\Models\Parcours::where('ppr', $user->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->with(['entite.parent', 'grade'])
            ->orderBy('date_debut', 'desc')
            ->first();
        
        // Get grade name
        $gradeName = $user->userInfo && $user->userInfo->grade 
            ? strtoupper($user->userInfo->grade->name) 
            : ($currentParcours && $currentParcours->grade 
                ? strtoupper($currentParcours->grade->name) 
                : '');
        
        // Get service name
        $serviceName = $currentParcours && $currentParcours->entite 
            ? $currentParcours->entite->name 
            : '';
        
        // Get parent entity name (direction)
        $parentEntityName = 'Direction du Capital Humain et de la Logistique'; // Default fallback
        if ($currentParcours && $currentParcours->entite && $currentParcours->entite->parent) {
            $parentEntityName = $currentParcours->entite->parent->name;
        }
        
        // Get logo data
        $logoData = '';
        try {
            if (extension_loaded('gd')) {
                $logoPath = public_path('images/anef.png');
                if (file_exists($logoPath)) {
                    $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                }
            }
        } catch (\Exception $e) {
            $logoData = '';
        }
        
        return [
            'currentParcours' => $currentParcours,
            'gradeName' => $gradeName,
            'serviceName' => $serviceName,
            'parentEntityName' => $parentEntityName,
            'logoData' => $logoData,
        ];
    }

    /**
     * Prepare QR code data for PDF generation
     */
    private function prepareQRCodeData(?string $verificationUrl): ?string
    {
        if (!$verificationUrl) {
            return null;
        }
        
        try {
            if (extension_loaded('gd')) {
                $qrSvg = QrCode::size(70)->format('svg')->generate($verificationUrl);
                return 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
            }
        } catch (\Exception $e) {
            \Log::warning('Error generating QR code: ' . $e->getMessage());
        }
        
        return null;
    }
}


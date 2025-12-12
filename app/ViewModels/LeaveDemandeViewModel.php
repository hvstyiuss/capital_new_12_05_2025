<?php

namespace App\ViewModels;

use App\Models\Demande;
use Carbon\Carbon;

class LeaveDemandeViewModel
{
    public function __construct(
        public Demande $demande,
    ) {
        $this->prepareData();
    }

    public ?\App\Models\AvisDepart $avisDepart = null;
    public ?\App\Models\AvisRetour $avisRetour = null;

    protected function prepareData(): void
    {
        // Ensure relationships are loaded
        if (!$this->demande->relationLoaded('avis')) {
            $this->demande->load('avis.avisDepart', 'avis.avisRetour');
        }

        $avis = $this->demande->avis;
        $this->avisDepart = $avis?->avisDepart;
        $this->avisRetour = $avis?->avisRetour;
    }

    /**
     * Get the status for the main card border
     */
    public function getStatus(): string
    {
        return $this->avisDepart?->statut ?? 'pending';
    }

    /**
     * Get the border color class
     */
    public function getBorderColorClass(): string
    {
        $status = $this->getStatus();
        return match($status) {
            'pending' => 'border-left-pending',
            'approved' => 'border-left-approved',
            'rejected' => 'border-left-rejected',
            'cancelled' => 'border-left-cancelled',
            default => 'border-left-pending',
        };
    }

    /**
     * Get type demande label
     */
    public function getTypeDemande(): string
    {
        if ($this->demande->demandeConge?->typeConge) {
            return $this->demande->demandeConge->typeConge->name;
        }

        return 'Congé Administratif Annuel';
    }

    /**
     * Get formatted date depot
     */
    public function getFormattedDateDepot(): string
    {
        return $this->demande->created_at->format('d/m/Y H:i');
    }

    /**
     * Get user name
     */
    public function getUserName(): string
    {
        if (!$this->demande->user) {
            return 'N/A';
        }

        return $this->demande->user->fname . ' ' . $this->demande->user->lname;
    }

    /**
     * Check if demande is pending
     */
    public function isPending(): bool
    {
        return $this->getStatus() === 'pending';
    }

    /**
     * Check if demande is fully approved
     */
    public function isFullyApproved(): bool
    {
        return $this->getStatus() === 'approved' 
            && $this->avisRetour?->statut === 'approved';
    }

    /**
     * Check if consumption exceeds
     */
    public function hasConsumptionExceeds(): bool
    {
        return $this->avisRetour?->consumption_exceeds ?? false;
    }
}


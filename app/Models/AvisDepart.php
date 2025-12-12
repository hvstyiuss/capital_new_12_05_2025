<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisDepart extends Model
{
    protected $fillable = [
        'avis_id',
        'nb_jours_demandes',
        'date_depart',
        'date_retour',
        'odf',
        'statut',
        'pdf_path',
        'verification_code',
    ];

    protected $casts = [
        'date_depart' => 'date',
        'date_retour' => 'date',
    ];

    /**
     * Get the notice that owns this departure notice.
     */
    public function avis(): BelongsTo
    {
        return $this->belongsTo(Avis::class);
    }

    /**
     * Get the status label in French
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'cancelled' => 'Annulé',
            default => 'En attente',
        };
    }

    /**
     * Get the badge class for the status
     */
    public function getBadgeClassAttribute(): string
    {
        return match($this->statut) {
            'approved' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'rejected' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the border color class for the status
     */
    public function getBorderColorClassAttribute(): string
    {
        return match($this->statut) {
            'pending' => 'border-left-pending',
            'approved' => 'border-left-approved',
            'rejected' => 'border-left-rejected',
            'cancelled' => 'border-left-cancelled',
            default => 'border-left-pending',
        };
    }

    /**
     * Check if PDF can be downloaded
     */
    public function getCanDownloadPdfAttribute(): bool
    {
        return $this->statut === 'approved' 
            && $this->id !== null 
            && $this->pdf_path !== null;
    }

    /**
     * Check if can be validated
     */
    public function getCanBeValidatedAttribute(): bool
    {
        return $this->statut === 'pending' && $this->id !== null;
    }

    /**
     * Get formatted date depart
     */
    public function getFormattedDateDepartAttribute(): ?string
    {
        return $this->date_depart ? $this->date_depart->format('d/m/Y') : null;
    }

    /**
     * Get formatted date retour
     */
    public function getFormattedDateRetourAttribute(): ?string
    {
        return $this->date_retour ? $this->date_retour->format('d/m/Y') : null;
    }

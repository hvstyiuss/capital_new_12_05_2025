<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AvisRetour extends Model
{
    protected $fillable = [
        'avis_id',
        'date_retour_declaree',
        'date_retour_effectif',
        'nbr_jours_consumes',
        'statut',
        'pdf_path',
        'verification_code',
        'explanation_required',
        'explanation_deadline',
        'explanation_provided',
        'explanation_pdf_path',
    ];

    protected $casts = [
        'date_retour_declaree' => 'date',
        'date_retour_effectif' => 'date',
        'explanation_required' => 'boolean',
        'explanation_deadline' => 'datetime',
    ];

    /**
     * Get the notice that owns this return notice.
     */
    public function avis(): BelongsTo
    {
        return $this->belongsTo(Avis::class);
    }

    /**
     * Get the status label in French
     */
    public function getStatutLabelAttribute(): ?string
    {
        if (!$this->statut) {
            return null;
        }

        return match($this->statut) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'cancelled' => 'Annulé',
            default => null,
        };
    }

    /**
     * Get the badge class for the status
     */
    public function getBadgeClassAttribute(): string
    {
        if (!$this->statut) {
            return 'bg-secondary';
        }

        return match($this->statut) {
            'approved' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'rejected' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if PDF can be downloaded
     */
    public function getCanDownloadPdfAttribute(): bool
    {
        return $this->statut === 'approved' 
            && $this->id !== null 
            && ($this->pdf_path !== null || $this->explanation_pdf_path !== null);
    }

    /**
     * Get the PDF route name to use
     */
    public function getPdfRouteNameAttribute(): ?string
    {
        if (!$this->can_download_pdf) {
            return null;
        }

        if ($this->pdf_path) {
            return 'hr.leaves.download-avis-retour-pdf';
        }

        if ($this->explanation_pdf_path) {
            return 'hr.leaves.download-explanation-pdf';
        }

        return null;
    }

    /**
     * Check if can be validated
     */
    public function getCanBeValidatedAttribute(): bool
    {
        return $this->statut === 'pending' && $this->id !== null;
    }

    /**
     * Check if consumption exceeds declared return date
     */
    public function getConsumptionExceedsAttribute(): bool
    {
        if (!$this->date_retour_declaree || !$this->date_retour_effectif) {
            return false;
        }

        return Carbon::parse($this->date_retour_effectif)
            ->greaterThan(Carbon::parse($this->date_retour_declaree));
    }

    /**
     * Get formatted date retour declaree
     */
    public function getFormattedDateRetourDeclareeAttribute(): ?string
    {
        return $this->date_retour_declaree ? $this->date_retour_declaree->format('d/m/Y') : null;
    }

    /**
     * Get formatted date retour effectif
     */
    public function getFormattedDateRetourEffectifAttribute(): ?string
    {
        return $this->date_retour_effectif ? $this->date_retour_effectif->format('d/m/Y') : null;
    }

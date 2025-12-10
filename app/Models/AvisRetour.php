<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisRetour extends Model
{
    protected $fillable = [
        'avis_id',
        'date_retour_declaree',
        'date_retour_effectif',
        'nbr_jours_consumes',
        'statut',
        'pdf_path',
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
}






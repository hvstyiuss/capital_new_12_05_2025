<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongeExceptionnel extends Model
{
    protected $fillable = [
        'demande_conge_id',
        'type_exceptionnel_id',
        'date_evenement',
        'nbr_jours_demandees',
        'motif',
    ];

    protected $casts = [
        'date_evenement' => 'date',
    ];

    /**
     * Get the demande conge associated with this exceptional leave.
     */
    public function demandeConge(): BelongsTo
    {
        return $this->belongsTo(DemandeConge::class, 'demande_conge_id');
    }

    /**
     * Get the type of exceptional leave.
     */
    public function typeExceptionnel(): BelongsTo
    {
        return $this->belongsTo(TypeExcep::class, 'type_exceptionnel_id');
    }
}








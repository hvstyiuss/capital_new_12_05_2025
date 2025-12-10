<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DemandeConge extends Model
{
    protected $fillable = [
        'demande_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nbr_jours_demandes',
        'motif',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Get the demande associated with this leave request.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    /**
     * Get the type of leave.
     */
    public function typeConge(): BelongsTo
    {
        return $this->belongsTo(TypeConge::class, 'type_conge_id');
    }

    /**
     * Get the sickness leave details if this is a sick leave.
     */
    public function congeMaladie(): HasOne
    {
        return $this->hasOne(CongeMaladie::class, 'demande_conge_id');
    }

    /**
     * Get the exceptional leave details if this is an exceptional leave.
     */
    public function congeExceptionnel(): HasOne
    {
        return $this->hasOne(CongeExceptionnel::class, 'demande_conge_id');
    }
}








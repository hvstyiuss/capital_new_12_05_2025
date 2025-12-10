<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conge extends Model
{
    protected $fillable = [
        'ppr',
        'annee',
        'demande_id',
        'reference_decision',
        'reliquat_annee_anterieure',
        'reliquat_annee_courante',
        'cumul_jours_consommes',
    ];

    /**
     * Get the request associated with this leave.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    /**
     * Get the sickness leave associated with this leave.
     */
    public function maladie(): HasOne
    {
        return $this->hasOne(Maladie::class);
    }

    /**
     * Get the exceptional leave associated with this leave.
     */
    public function exceptionnel(): HasOne
    {
        return $this->hasOne(Exceptionnel::class);
    }

    /**
     * Get the annual leave associated with this leave.
     */
    public function annuelle(): HasOne
    {
        return $this->hasOne(Annuelle::class);
    }

    /**
     * Get the notice associated with this leave.
     */
    public function avis(): HasOne
    {
        return $this->hasOne(Avis::class);
    }
}

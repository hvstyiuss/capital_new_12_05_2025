<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Avis extends Model
{
    protected $fillable = [
        'demande_id',
        'date_depot',
        'is_validated',
    ];

    protected $casts = [
        'date_depot' => 'date',
        'is_validated' => 'boolean',
    ];

    /**
     * Get the demande associated with this notice.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    /**
     * Get the departure notice associated with this notice.
     */
    public function avisDepart(): HasOne
    {
        return $this->hasOne(AvisDepart::class);
    }

    /**
     * Get the return notice associated with this notice.
     */
    public function avisRetour(): HasOne
    {
        return $this->hasOne(AvisRetour::class);
    }
}






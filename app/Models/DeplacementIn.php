<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeplacementIn extends Model
{
    protected $fillable = [
        'deplacement_id',
        'objet',
        'mois',
        'deplacement_periode_id',
    ];

    /**
     * Get the deplacement for this deplacement_in.
     */
    public function deplacement(): BelongsTo
    {
        return $this->belongsTo(Deplacement::class);
    }

    /**
     * Get the periode for this deplacement_in.
     */
    public function periode(): BelongsTo
    {
        return $this->belongsTo(DeplacementPeriode::class, 'deplacement_periode_id');
    }
}




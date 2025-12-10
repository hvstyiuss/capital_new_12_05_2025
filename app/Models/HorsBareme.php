<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\DeplacementPeriode;

class HorsBareme extends Model
{
    protected $table = 'hors_baremes';

    protected $fillable = [
        'ppr',
        'nb_jours',
        'deplacement_periode_id',
        'annee',
        'categorie',
    ];

    /**
     * Get the user for this hors bareme.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the periode for this hors bareme.
     */
    public function periode(): BelongsTo
    {
        return $this->belongsTo(DeplacementPeriode::class, 'deplacement_periode_id');
    }
}


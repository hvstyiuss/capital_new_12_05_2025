<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deplacement extends Model
{
    protected $fillable = [
        'ppr',
        'date_debut',
        'date_fin',
        'nbr_jours',
        'echelle_tarifs_id',
        'somme',
        'annee',
        'type_in_out',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'somme' => 'decimal:2',
    ];

    /**
     * Get the user for this deplacement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the deplacement_ins for this deplacement.
     */
    public function deplacementIns(): HasMany
    {
        return $this->hasMany(DeplacementIn::class, 'deplacement_id');
    }

    /**
     * Get the echelle tarif for this deplacement.
     */
    public function echelleTarif(): BelongsTo
    {
        return $this->belongsTo(EchelleTarif::class, 'echelle_tarifs_id');
    }
}



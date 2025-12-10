<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Demande extends Model
{
    protected $fillable = [
        'date_debut',
        'ppr',
        'type',
        'entite_id',
        'created_by',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
    ];

    /**
     * Get the user who made this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the leave request associated with this demand.
     */
    public function conge(): HasOne
    {
        return $this->hasOne(Conge::class);
    }

    /**
     * Get the transfer request associated with this demand.
     */
    public function mutation(): HasOne
    {
        return $this->hasOne(Mutation::class);
    }

    /**
     * Get the notice associated with this demand.
     */
    public function avis(): HasOne
    {
        return $this->hasOne(Avis::class);
    }

    /**
     * Get the demande conge associated with this demand (if type is 'conge').
     */
    public function demandeConge(): HasOne
    {
        return $this->hasOne(DemandeConge::class);
    }

    /**
     * Get the demande mutation associated with this demand (if type is 'mutation').
     */
    public function demandeMutation(): HasOne
    {
        return $this->hasOne(DemandeMutation::class);
    }

    /**
     * Get the entity associated with this demand.
     */
    public function entite(): BelongsTo
    {
        return $this->belongsTo(Entite::class);
    }

    /**
     * Get the creator of this demand.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'ppr');
    }
}

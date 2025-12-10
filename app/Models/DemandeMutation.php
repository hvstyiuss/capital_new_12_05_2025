<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeMutation extends Model
{
    protected $fillable = [
        'demande_id',
        'mutation_id',
    ];

    /**
     * Get the demande associated with this mutation request.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    /**
     * Get the mutation associated with this demande.
     */
    public function mutation(): BelongsTo
    {
        return $this->belongsTo(Mutation::class);
    }
}








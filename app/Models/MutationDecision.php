<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutationDecision extends Model
{
    protected $table = 'decisions';

    protected $fillable = [
        'type',
        'reference_id',
        'collaborateur_rh_ppr',
        'date_affectation',
        'decision_text',
    ];

    protected $casts = [
        'date_affectation' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($decision) {
            // Set default type to 'mutation' if not provided
            if (empty($decision->type)) {
                $decision->type = 'mutation';
            }
        });
    }

    /**
     * Get the mutation for this decision (when type is 'mutation').
     */
    public function mutation(): BelongsTo
    {
        return $this->belongsTo(Mutation::class, 'reference_id');
    }

    /**
     * Get the collaborateur RH who created this decision.
     */
    public function collaborateurRh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collaborateur_rh_ppr', 'ppr');
    }

    /**
     * Scope to get only mutation decisions.
     */
    public function scopeMutations($query)
    {
        return $query->where('type', 'mutation');
    }

    /**
     * Scope to get decisions by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

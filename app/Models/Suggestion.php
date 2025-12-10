<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    protected $fillable = [
        'ppr',
        'sujet',
        'message',
        'statut',
        'reponse',
        'repondu_par',
        'repondu_le',
    ];

    protected $casts = [
        'repondu_le' => 'datetime',
    ];

    /**
     * Get the user who made this suggestion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the user who replied to this suggestion.
     */
    public function reponduPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repondu_par', 'ppr');
    }
}

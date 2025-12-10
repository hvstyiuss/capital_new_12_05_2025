<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteAnnuelle extends Model
{
    protected $fillable = [
        'annee',
        'note',
        'ppr',
        'observation',
    ];

    protected $casts = [
        'note' => 'decimal:2',
    ];

    /**
     * Get the user for this annual note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }
}

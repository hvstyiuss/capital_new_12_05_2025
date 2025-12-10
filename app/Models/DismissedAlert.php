<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DismissedAlert extends Model
{
    protected $fillable = [
        'ppr',
        'demande_id',
        'alert_type',
    ];

    /**
     * Get the user that dismissed this alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the demande related to this dismissed alert.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }
}












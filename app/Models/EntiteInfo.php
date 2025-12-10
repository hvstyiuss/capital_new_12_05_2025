<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntiteInfo extends Model
{
    protected $fillable = [
        'entite_id',
        'description',
        'type',
    ];

    /**
     * Get the entity that owns this info.
     */
    public function entite(): BelongsTo
    {
        return $this->belongsTo(Entite::class);
    }

    // Additional computed attributes can be added here as needed
}

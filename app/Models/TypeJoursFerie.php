<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeJoursFerie extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the holidays for this type.
     */
    public function joursFeries(): HasMany
    {
        return $this->hasMany(JoursFerie::class, 'type_jours_ferie_id');
    }
}






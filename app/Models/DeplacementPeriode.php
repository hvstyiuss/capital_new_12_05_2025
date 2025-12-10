<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeplacementPeriode extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the deplacement_ins for this periode.
     */
    public function deplacementIns(): HasMany
    {
        return $this->hasMany(DeplacementIn::class, 'deplacement_periode_id');
    }
}




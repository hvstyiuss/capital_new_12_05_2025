<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Echelle extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the grades for this Echelle.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the tarifs for this Echelle.
     */
    public function tarifs(): HasMany
    {
        return $this->hasMany(EchelleTarif::class);
    }
}







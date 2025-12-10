<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'nom',
        'ordre',
    ];

    protected $casts = [
        'ordre' => 'integer',
    ];

    public function criteres(): HasMany
    {
        return $this->hasMany(Critere::class);
    }
}




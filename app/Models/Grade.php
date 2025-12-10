<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    protected $fillable = [
        'name',
        'echelle_id',
    ];

    /**
     * Get the Echelle that owns this grade.
     */
    public function Echelle(): BelongsTo
    {
        return $this->belongsTo(Echelle::class);
    }

    /**
     * Get the user infos with this grade.
     */
    public function userInfos(): HasMany
    {
        return $this->hasMany(UserInfo::class);
    }

    /**
     * Get the career path entries with this grade.
     */
    public function parcours(): HasMany
    {
        return $this->hasMany(Parcours::class);
    }
}

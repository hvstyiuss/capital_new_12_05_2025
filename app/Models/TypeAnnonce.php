<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeAnnonce extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'couleur',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all announcements of this type.
     */
    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class, 'type_annonce_id');
    }
}

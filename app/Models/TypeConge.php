<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeConge extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get all leave requests of this type.
     */
    public function demandeConges(): HasMany
    {
        return $this->hasMany(DemandeConge::class, 'type_conge_id');
    }

    /**
     * Get the human-readable name.
     */
    public function getDisplayNameAttribute(): string
    {
        return match($this->name) {
            'annuel' => 'Congé Annuel',
            'maladie' => 'Congé Maladie',
            'exceptionnel' => 'Congé Exceptionnel',
            default => $this->name,
        };
    }
}








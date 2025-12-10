<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeMaladie extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get all sick leaves of this type.
     */
    public function congeMaladies(): HasMany
    {
        return $this->hasMany(CongeMaladie::class, 'type_maladie_id');
    }

    /**
     * Get the human-readable name.
     */
    public function getDisplayNameAttribute(): string
    {
        return match($this->name) {
            'cc' => 'Congé Maladie Courte Durée (≤ 6 mois)',
            'md' => 'Congé Maladie Moyenne Durée (≤ 3 ans)',
            'ld' => 'Congé Maladie Longue Durée (≤ 5 ans)',
            'm' => 'Congé Maternité (98 jours)',
            default => $this->name,
        };
    }

    /**
     * Get the maximum duration in days.
     */
    public function getMaxDurationDaysAttribute(): ?int
    {
        return match($this->name) {
            'cc' => 180, // 6 months
            'md' => 1095, // 3 years
            'ld' => 1825, // 5 years
            'm' => 98, // 14 weeks
            default => null,
        };
    }
}








<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Parcours extends Model
{
    protected $fillable = [
        'ppr',
        'entite_id',
        'poste',
        'role',
        'date_debut',
        'date_fin',
        'grade_id',
        'reason',
        'created_by_ppr',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Validate before saving
        static::saving(function ($parcours) {
            // Check if this parcours will be active (date_fin is null or in the future)
            $willBeActive = $parcours->date_fin === null || $parcours->date_fin >= now();
            
            if ($willBeActive) {
                // Validation: User can't have two active postes at the same time
                $existingActiveParcours = static::where('ppr', $parcours->ppr)
                    ->where('id', '!=', $parcours->id ?? 0)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->exists();
                
                if ($existingActiveParcours) {
                    throw ValidationException::withMessages([
                        'ppr' => 'L\'utilisateur ne peut pas avoir deux postes actifs en même temps. Veuillez terminer le poste actif avant d\'en créer un nouveau.'
                    ]);
                }
            }
        });
    }

    /**
     * Get the user for this career path entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the entity for this career path entry.
     */
    public function entite(): BelongsTo
    {
        return $this->belongsTo(Entite::class);
    }

    /**
     * Get the grade for this career path entry.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the user who created this parcours entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_ppr', 'ppr');
    }
}

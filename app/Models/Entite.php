<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Entite extends Model
{
    protected $fillable = [
        'code',
        'date_debut',
        'date_fin',
        'name',
        'parent_id',
        'type',
        'entity_type',
        'lieu_affectation',
        'lieu_direction',
        'chef_ppr',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Get the info for this entity.
     */
    public function entiteInfo(): HasOne
    {
        return $this->hasOne(EntiteInfo::class);
    }

    /**
     * Get the parent entity.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Entite::class, 'parent_id');
    }

    /**
     * Get the child entities.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Entite::class, 'parent_id');
    }

    /**
     * Get the users belonging to this entity.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parcours', 'entite_id', 'ppr')
            ->withTimestamps();
    }

    /**
     * Get the career path entries for this entity.
     */
    public function parcours(): HasMany
    {
        return $this->hasMany(Parcours::class);
    }

    /**
     * Get the announcements for this entity.
     */
    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class);
    }

    /**
     * Get the mutations targeting this entity.
     */
    public function mutations(): HasMany
    {
        return $this->hasMany(Mutation::class, 'to_entite_id');
    }

    /**
     * Get the chef user of this entity.
     */
    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_ppr', 'ppr');
    }

    /**
     * Get the head user of this entity (alias for chef).
     */
    public function headUser()
    {
        return $this->chef;
    }

    /**
     * Get the chef PPR for this entity.
     */
    public function getChefAttribute()
    {
        return $this->chef_ppr;
    }
}

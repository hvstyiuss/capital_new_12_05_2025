<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Annonce extends Model
{
    protected $fillable = [
        'content',
        'statut',
        'image',
        'type_annonce_id',
        'ppr',
    ];

    protected $table = 'annonces';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this announcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the type of this announcement.
     */
    public function typeAnnonce(): BelongsTo
    {
        return $this->belongsTo(TypeAnnonce::class, 'type_annonce_id');
    }

    /**
     * Get the entities for this announcement (many-to-many).
     */
    public function entites(): BelongsToMany
    {
        return $this->belongsToMany(Entite::class, 'annonce_entite', 'annonce_id', 'entite_id');
    }
}

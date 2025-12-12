<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserInfo extends Model
{
    protected $fillable = [
        'photo',
        'adresse',
        'gsm',
        'email',
        'cin',
        'rib',
        'grade_id',
        'echelle_id',
        'corps',
        'ppr',
        'responsable',
    ];

    /**
     * Get the user that owns this info.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    /**
     * Get the grade for this user info.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the echelle for this user info.
     */
    public function echelle(): BelongsTo
    {
        return $this->belongsTo(Echelle::class);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->fname . ' ' . $this->lname;
    }

    /**
     * Get the photo URL attribute.
     *
     * @return string|null
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        // Use asset() for local development compatibility
        // This generates relative URLs that work with any host/port
        return asset('storage/' . $this->photo);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoursFerie extends Model
{
    protected $table = 'jours_feries';

    protected $fillable = [
        'date',
        'name',
        'type_jours_ferie_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function typeJoursFerie(): BelongsTo
    {
        return $this->belongsTo(TypeJoursFerie::class, 'type_jours_ferie_id');
    }
}

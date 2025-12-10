<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionEvaluation extends Model
{
    protected $fillable = [
        'critere_id',
        'intitule',
        'score',
        'ordre',
    ];

    protected $casts = [
        'score' => 'integer',
        'ordre' => 'integer',
    ];

    public function critere(): BelongsTo
    {
        return $this->belongsTo(Critere::class);
    }
}




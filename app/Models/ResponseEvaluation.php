<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseEvaluation extends Model
{
    protected $fillable = [
        'evaluation_id',
        'critere_id',
        'option_id',
        'score_obtenu',
    ];

    protected $casts = [
        'score_obtenu' => 'integer',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function critere(): BelongsTo
    {
        return $this->belongsTo(Critere::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(OptionEvaluation::class, 'option_id');
    }
}




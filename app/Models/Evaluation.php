<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = [
        'ppr',
        'evalue_par',
        'total_score',
        'commentaire',
        'annee',
        'type_prime_id',
        'observation',
        'total_prime',
    ];

    protected $casts = [
        'total_score' => 'integer',
        'observation' => 'boolean',
        'total_prime' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evalue_par', 'ppr');
    }

    public function typePrime(): BelongsTo
    {
        return $this->belongsTo(EvaluationPrime::class, 'type_prime_id');
    }

    public function responseEvaluations(): HasMany
    {
        return $this->hasMany(ResponseEvaluation::class);
    }
}

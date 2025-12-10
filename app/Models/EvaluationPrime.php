<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationPrime extends Model
{
    protected $fillable = [
        'nom',
    ];

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'type_prime_id');
    }
}

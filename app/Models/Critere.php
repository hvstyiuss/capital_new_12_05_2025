<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Critere extends Model
{
    protected $fillable = [
        'category_id',
        'nom',
        'description',
        'ordre',
    ];

    protected $casts = [
        'ordre' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function optionEvaluations(): HasMany
    {
        return $this->hasMany(OptionEvaluation::class, 'critere_id');
    }
}




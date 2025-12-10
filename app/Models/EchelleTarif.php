<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EchelleTarif extends Model
{
    protected $fillable = [
        'echelle_id',
        'type_in_out_mission',
        'montant_deplacement',
        'max_jours',
    ];

    protected $casts = [
        'montant_deplacement' => 'decimal:2',
    ];

    /**
     * Get the echelle that owns this tarif.
     */
    public function echelle(): BelongsTo
    {
        return $this->belongsTo(Echelle::class);
    }
}

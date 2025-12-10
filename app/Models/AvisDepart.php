<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisDepart extends Model
{
    protected $fillable = [
        'avis_id',
        'nb_jours_demandes',
        'date_depart',
        'date_retour',
        'odf',
        'statut',
        'pdf_path',
    ];

    protected $casts = [
        'date_depart' => 'date',
        'date_retour' => 'date',
    ];

    /**
     * Get the notice that owns this departure notice.
     */
    public function avis(): BelongsTo
    {
        return $this->belongsTo(Avis::class);
    }
}






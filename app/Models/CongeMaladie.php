<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongeMaladie extends Model
{
    protected $fillable = [
        'demande_conge_id',
        'type_maladie_id',
        'date_declaration',
        'date_constatation',
        'date_prolongation',
        'date_reprise_travail',
        'nbr_jours_arret',
        'nbr_jours_prolongation',
        'nbr_jours_total',
        'reference_arret',
        'reference_prolongation',
        'observation',
    ];

    protected $casts = [
        'date_declaration' => 'date',
        'date_constatation' => 'date',
        'date_prolongation' => 'date',
        'date_reprise_travail' => 'date',
    ];

    /**
     * Get the demande conge associated with this sick leave.
     */
    public function demandeConge(): BelongsTo
    {
        return $this->belongsTo(DemandeConge::class, 'demande_conge_id');
    }

    /**
     * Get the type of sickness leave.
     */
    public function typeMaladie(): BelongsTo
    {
        return $this->belongsTo(TypeMaladie::class, 'type_maladie_id');
    }
}








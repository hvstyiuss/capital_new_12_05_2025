<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mutation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'ppr',
        'to_entite_id',
        'motif',
        'mutation_type',
        'is_validated_ent',
        'decision_conducteur_rh',
        'valide_reception',
        'valide_par',
        'approved_by_current_direction',
        'approved_by_current_direction_ppr',
        'approved_by_current_direction_at',
        'sent_to_destination_by_super_rh',
        'sent_to_destination_by_super_rh_ppr',
        'sent_to_destination_by_super_rh_at',
        'rejected_by_super_rh',
        'rejected_by_super_rh_ppr',
        'rejection_reason_super_rh',
        'rejected_by_super_rh_at',
        'approved_by_destination_direction',
        'approved_by_destination_direction_ppr',
        'approved_by_destination_direction_at',
        'rejected_by_current_direction',
        'rejected_by_current_direction_ppr',
        'rejection_reason_current',
        'rejected_by_current_direction_at',
        'rejected_by_destination_direction',
        'rejected_by_destination_direction_ppr',
        'rejection_reason_destination',
        'rejected_by_destination_direction_at',
        'approved_by_super_collaborateur_rh',
        'approved_by_super_collaborateur_rh_ppr',
        'approved_by_super_collaborateur_rh_at',
        'date_debut_affectation',
    ];

    protected $casts = [
        'is_validated_ent' => 'boolean',
        'valide_reception' => 'boolean',
        'approved_by_current_direction' => 'boolean',
        'sent_to_destination_by_super_rh' => 'boolean',
        'rejected_by_super_rh' => 'boolean',
        'approved_by_destination_direction' => 'boolean',
        'rejected_by_current_direction' => 'boolean',
        'rejected_by_destination_direction' => 'boolean',
        'approved_by_super_collaborateur_rh' => 'boolean',
        'approved_by_current_direction_at' => 'datetime',
        'sent_to_destination_by_super_rh_at' => 'datetime',
        'rejected_by_super_rh_at' => 'datetime',
        'approved_by_destination_direction_at' => 'datetime',
        'rejected_by_current_direction_at' => 'datetime',
        'rejected_by_destination_direction_at' => 'datetime',
        'approved_by_super_collaborateur_rh_at' => 'datetime',
        'date_debut_affectation' => 'date',
    ];

    public function toEntite(): BelongsTo
    {
        return $this->belongsTo(Entite::class, 'to_entite_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par', 'ppr');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ppr', 'ppr');
    }

    public function approvedByCurrentDirection(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_current_direction_ppr', 'ppr');
    }

    public function approvedByDestinationDirection(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_destination_direction_ppr', 'ppr');
    }

    public function rejectedByCurrentDirection(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_current_direction_ppr', 'ppr');
    }

    public function rejectedByDestinationDirection(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_destination_direction_ppr', 'ppr');
    }

    public function approvedBySuperCollaborateurRh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_super_collaborateur_rh_ppr', 'ppr');
    }

    public function sentToDestinationBySuperRh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_to_destination_by_super_rh_ppr', 'ppr');
    }

    public function rejectedBySuperRh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_super_rh_ppr', 'ppr');
    }

    public function decision(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MutationDecision::class, 'reference_id')
                    ->where('type', 'mutation');
    }
}

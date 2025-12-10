<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MutationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'to_entite_id' => $this->to_entite_id,
            'mutation_type' => $this->mutation_type,
            'motif' => $this->motif,
            'is_validated_ent' => $this->is_validated_ent,
            'valide_reception' => $this->valide_reception,
            'decision_conducteur_rh' => $this->decision_conducteur_rh,
            'approved_by_current_direction' => $this->approved_by_current_direction,
            'approved_by_current_direction_at' => $this->approved_by_current_direction_at,
            'approved_by_destination_direction' => $this->approved_by_destination_direction,
            'approved_by_destination_direction_at' => $this->approved_by_destination_direction_at,
            'approved_by_super_collaborateur_rh' => $this->approved_by_super_collaborateur_rh,
            'approved_by_super_collaborateur_rh_at' => $this->approved_by_super_collaborateur_rh_at,
            'rejected_by_current_direction' => $this->rejected_by_current_direction,
            'rejected_by_current_direction_at' => $this->rejected_by_current_direction_at,
            'rejection_reason_current' => $this->rejection_reason_current,
            'rejected_by_destination_direction' => $this->rejected_by_destination_direction,
            'rejected_by_destination_direction_at' => $this->rejected_by_destination_direction_at,
            'rejection_reason_destination' => $this->rejection_reason_destination,
            'rejected_by_super_rh' => $this->rejected_by_super_rh,
            'rejected_by_super_rh_at' => $this->rejected_by_super_rh_at,
            'rejection_reason_super_rh' => $this->rejection_reason_super_rh,
            'sent_to_destination_by_super_rh' => $this->sent_to_destination_by_super_rh,
            'sent_to_destination_by_super_rh_at' => $this->sent_to_destination_by_super_rh_at,
            'date_debut_affectation' => $this->date_debut_affectation,
            'user' => new UserResource($this->whenLoaded('user')),
            'to_entite' => new EntiteResource($this->whenLoaded('toEntite')),
            'approved_by_current_direction_user' => new UserResource($this->whenLoaded('approvedByCurrentDirection')),
            'approved_by_destination_direction_user' => new UserResource($this->whenLoaded('approvedByDestinationDirection')),
            'approved_by_super_collaborateur_rh_user' => new UserResource($this->whenLoaded('approvedBySuperCollaborateurRh')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}





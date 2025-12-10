<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvisRetourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'avis_id' => $this->avis_id,
            'nbr_jours_consumes' => $this->nbr_jours_consumes,
            'date_retour_declaree' => $this->date_retour_declaree,
            'date_retour_effectif' => $this->date_retour_effectif,
            'statut' => $this->statut,
            'avis' => new AvisResource($this->whenLoaded('avis')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}





<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvisDepartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'avis_id' => $this->avis_id,
            'date_depart' => $this->date_depart,
            'statut' => $this->statut,
            'avis' => new AvisResource($this->whenLoaded('avis')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}





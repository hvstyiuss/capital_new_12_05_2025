<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'demande_id' => $this->demande_id,
            'avis' => $this->avis,
            'date_avis' => $this->date_avis,
            'user' => new UserResource($this->whenLoaded('user')),
            'demande' => new DemandeResource($this->whenLoaded('demande')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}





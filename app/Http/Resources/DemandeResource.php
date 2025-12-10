<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'type' => $this->type,
            'date_depot' => $this->date_depot,
            'statut' => $this->statut,
            'user' => new UserResource($this->whenLoaded('user')),
            'avis' => AvisResource::collection($this->whenLoaded('avis')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}





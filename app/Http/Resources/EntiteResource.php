<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'chef' => $this->chef, // Uses accessor from Entite model
            'parent' => new EntiteResource($this->whenLoaded('parent')),
            'children' => EntiteResource::collection($this->whenLoaded('children')),
            'entite_info' => new EntiteInfoResource($this->whenLoaded('entiteInfo')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}




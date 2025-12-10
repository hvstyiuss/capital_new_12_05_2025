<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnonceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'content' => $this->content,
            'image' => $this->image,
            'statut' => $this->statut,
            'type_annonce_id' => $this->type_annonce_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'type_annonce' => $this->whenLoaded('typeAnnonce'),
            'entites' => EntiteResource::collection($this->whenLoaded('entites')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}





<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CritereResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'ordre' => $this->ordre,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'option_evaluations' => OptionEvaluationResource::collection($this->whenLoaded('optionEvaluations')),
        ];
    }
}




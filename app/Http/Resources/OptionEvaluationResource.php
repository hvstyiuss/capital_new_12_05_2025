<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionEvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'intitule' => $this->intitule,
            'score' => $this->score,
            'ordre' => $this->ordre,
            'critere' => new CritereResource($this->whenLoaded('critere')),
        ];
    }
}




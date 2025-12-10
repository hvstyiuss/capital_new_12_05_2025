<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseEvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'evaluation_id' => $this->evaluation_id,
            'critere_id' => $this->critere_id,
            'option_id' => $this->option_id,
            'score_obtenu' => $this->score_obtenu,
            'critere' => new CritereResource($this->whenLoaded('critere')),
            'option' => new OptionEvaluationResource($this->whenLoaded('option')),
        ];
    }
}




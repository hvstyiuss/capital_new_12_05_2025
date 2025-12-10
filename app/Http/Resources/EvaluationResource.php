<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'evalue_par' => $this->evalue_par,
            'total_score' => $this->total_score,
            'commentaire' => $this->commentaire,
            'annee' => $this->annee,
            'type_prime_id' => $this->type_prime_id,
            'observation' => $this->observation,
            'total_prime' => $this->total_prime,
            'user' => new UserResource($this->whenLoaded('user')),
            'evaluator' => new UserResource($this->whenLoaded('evaluator')),
            'response_evaluations' => ResponseEvaluationResource::collection($this->whenLoaded('responseEvaluations')),
            'type_prime' => new EvaluationPrimeResource($this->whenLoaded('typePrime')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


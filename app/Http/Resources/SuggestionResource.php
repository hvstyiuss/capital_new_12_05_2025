<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'sujet' => $this->sujet,
            'message' => $this->message,
            'statut' => $this->statut,
            'reponse' => $this->reponse,
            'repondu_par' => $this->repondu_par,
            'repondu_le' => $this->repondu_le?->toISOString(),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'ppr' => $this->user->ppr,
                    'fname' => $this->user->fname,
                    'lname' => $this->user->lname,
                    'email' => $this->user->email,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}





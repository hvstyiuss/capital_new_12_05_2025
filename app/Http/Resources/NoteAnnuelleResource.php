<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteAnnuelleResource extends JsonResource
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
            'annee' => $this->annee,
            'note' => $this->note,
            'observation' => $this->observation,
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





<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JoursFerieResource extends JsonResource
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
            'date' => $this->date?->format('Y-m-d'),
            'name' => $this->name,
            'type_jours_ferie_id' => $this->type_jours_ferie_id,
            'type_jours_ferie' => $this->whenLoaded('typeJoursFerie', function () {
                return [
                    'id' => $this->typeJoursFerie->id,
                    'name' => $this->typeJoursFerie->name,
                ];
            }),
            'year' => $this->date?->format('Y'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}





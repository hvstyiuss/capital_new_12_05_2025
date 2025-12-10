<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParcoursResource extends JsonResource
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
            'entite_id' => $this->entite_id,
            'entite' => $this->whenLoaded('entite', function () {
                return [
                    'id' => $this->entite->id,
                    'name' => $this->entite->name,
                ];
            }),
            'poste' => $this->poste,
            'role' => $this->role,
            'date_debut' => $this->date_debut?->format('Y-m-d'),
            'date_fin' => $this->date_fin?->format('Y-m-d'),
            'grade_id' => $this->grade_id,
            'grade' => $this->whenLoaded('grade', function () {
                return [
                    'id' => $this->grade->id,
                    'name' => $this->grade->name,
                ];
            }),
            'reason' => $this->reason,
            'created_by_ppr' => $this->created_by_ppr,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'ppr' => $this->user->ppr,
                    'fname' => $this->user->fname,
                    'lname' => $this->user->lname,
                    'email' => $this->user->email,
                ];
            }),
            'is_active' => $this->date_fin === null || $this->date_fin >= now(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}





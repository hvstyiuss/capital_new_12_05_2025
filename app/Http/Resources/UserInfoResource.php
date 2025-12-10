<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ppr' => $this->ppr,
            'fname' => $this->fname ?? null,
            'lname' => $this->lname ?? null,
            'photo' => $this->photo ?? null,
            'adresse' => $this->adresse ?? null,
            'email' => $this->email ?? null,
            'cin' => $this->cin ?? null,
            'rib' => $this->rib ?? null,
            'grade' => $this->whenLoaded('grade'),
            'echelle' => $this->whenLoaded('echelle'),
            'corps' => $this->corps ?? null,
            'status_retrait' => $this->status_retrait ?? null,
        ];
    }
}




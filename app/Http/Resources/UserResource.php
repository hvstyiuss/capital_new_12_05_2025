<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ppr' => $this->ppr,
            'name' => $this->name,
            'image' => $this->image,
            'email' => $this->email ?? null,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'user_info' => new UserInfoResource($this->whenLoaded('userInfo')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'entites' => EntiteResource::collection($this->whenLoaded('entites')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}




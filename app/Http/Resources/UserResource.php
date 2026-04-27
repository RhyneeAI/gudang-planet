<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role->value, 
            'role_label' => $this->role->label(),
            'company_id' => $this->company_id,
            'company_name' => $this->whenLoaded('company', fn() => $this->company->name),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
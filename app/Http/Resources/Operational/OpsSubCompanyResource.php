<?php

namespace App\Http\Resources\Operational;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpsSubCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => (string) $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'is_active' => (bool) $this->is_active,
            'mandor' => $this->whenLoaded('mandor', fn () => [
                'uuid' => $this->mandor->uuid,
                'name' => $this->mandor->name,
            ]),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'uuid' => $this->createdBy->uuid,
                'name' => $this->createdBy->name,
            ]),
            'wallet' => $this->whenLoaded('wallet', fn () => new OpsWalletResource($this->wallet)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

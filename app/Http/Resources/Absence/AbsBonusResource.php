<?php

namespace App\Http\Resources\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsBonusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => (string) $this->ulid,
            'reason' => $this->reason,
            'amount' => (float) $this->amount,
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'name' => $this->createdBy->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

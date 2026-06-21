<?php

namespace App\Http\Resources\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsShiftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => (string) $this->uuid,
            'name' => $this->name,
            'start_time' => substr((string) $this->start_time, 0, 5),
            'end_time' => substr((string) $this->end_time, 0, 5),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

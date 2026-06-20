<?php

namespace App\Http\Resources\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsEmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => (string) $this->uuid,
            'name' => $this->name,
            'phone' => $this->phone,
            'is_active' => (bool) $this->is_active,
            'profile' => $this->whenLoaded('absEmployeeProfile', function () {
                return [
                    'daily_rate' => (float) $this->absEmployeeProfile->daily_rate,
                    'branch' => $this->absEmployeeProfile->relationLoaded('branch')
                        ? new AbsBranchResource($this->absEmployeeProfile->branch)
                        : null,
                    'shift' => $this->absEmployeeProfile->relationLoaded('shift')
                        ? new AbsShiftResource($this->absEmployeeProfile->shift)
                        : null,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

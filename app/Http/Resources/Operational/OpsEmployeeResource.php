<?php

namespace App\Http\Resources\Operational;

use App\Http\Resources\Absence\AbsJabatanResource;
use App\Http\Resources\Absence\AbsShiftResource;
use App\Http\Resources\SubCompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpsEmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => (string) $this->uuid,
            'name' => $this->name,
            'phone' => $this->phone,
            'role' => $this->role?->value,
            'is_active' => (bool) $this->is_active,
            'profile' => $this->whenLoaded('absEmployeeProfile', function () {
                return [
                    'jabatan' => $this->absEmployeeProfile->relationLoaded('jabatan') && $this->absEmployeeProfile->jabatan
                        ? new AbsJabatanResource($this->absEmployeeProfile->jabatan)
                        : null,
                    'sub_company' => $this->absEmployeeProfile->relationLoaded('subCompany')
                        ? new SubCompanyResource($this->absEmployeeProfile->subCompany)
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

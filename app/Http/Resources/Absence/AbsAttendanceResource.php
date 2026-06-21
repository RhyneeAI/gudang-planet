<?php

namespace App\Http\Resources\Absence;

use App\Http\Resources\SubCompanyResource;
use App\Services\Absence\AbsFileService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fileService = app(AbsFileService::class);

        return [
            'ulid' => (string) $this->ulid,
            'date' => $this->date?->toDateString(),
            'check_in_time' => $this->check_in_time ? substr((string) $this->check_in_time, 0, 8) : null,
            'check_out_time' => $this->check_out_time ? substr((string) $this->check_out_time, 0, 8) : null,
            'check_in_photo' => $fileService->url($this->check_in_photo),
            'check_out_photo' => $fileService->url($this->check_out_photo),
            'status' => $this->status?->value,
            'late_reason' => $this->late_reason,
            'early_reason' => $this->early_reason,
            'employee' => $this->whenLoaded('user', fn () => [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
            ]),
            'sub_company' => $this->whenLoaded('subCompany', fn () => new SubCompanyResource($this->subCompany)),
            'shift' => $this->whenLoaded('shift', fn () => new AbsShiftResource($this->shift)),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

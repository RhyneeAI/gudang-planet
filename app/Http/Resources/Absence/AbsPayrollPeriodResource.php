<?php

namespace App\Http\Resources\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsPayrollPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => (string) $this->ulid,
            'period_month' => (int) $this->period_month,
            'period_year' => (int) $this->period_year,
            'daily_rate' => (float) $this->daily_rate,
            'total_days' => (int) $this->total_days,
            'gross_salary' => (float) $this->gross_salary,
            'total_deduction' => (float) $this->total_deduction,
            'total_bonus' => (float) $this->total_bonus,
            'net_salary' => (float) $this->net_salary,
            'status' => $this->status?->value,
            'notes' => $this->notes,
            'generated_at' => $this->generated_at?->toISOString(),
            'employee' => $this->whenLoaded('user', fn () => [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
            ]),
            'deductions' => AbsDeductionResource::collection($this->whenLoaded('deductions')),
            'bonuses' => AbsBonusResource::collection($this->whenLoaded('bonuses')),
        ];
    }
}

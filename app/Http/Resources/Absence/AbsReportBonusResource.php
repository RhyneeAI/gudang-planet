<?php

namespace App\Http\Resources\Absence;

use App\Http\Resources\SubCompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsReportBonusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subCompany = $this->user?->absEmployeeProfile?->subCompany;

        return [
            'ulid' => (string) $this->ulid,
            'date' => $this->created_at?->toDateString(),
            'reason' => $this->reason,
            'amount' => (float) $this->amount,
            'employee' => $this->whenLoaded('user', fn () => [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
            ]),
            'sub_company' => $subCompany ? new SubCompanyResource($subCompany) : null,
            'payroll_period' => $this->whenLoaded('payrollPeriod', fn () => [
                'ulid' => (string) $this->payrollPeriod->ulid,
                'period_month' => (int) $this->payrollPeriod->period_month,
                'period_year' => (int) $this->payrollPeriod->period_year,
            ]),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'name' => $this->createdBy->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

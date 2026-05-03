<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid'               => $this->ulid,
            'transaction_code'   => $this->transaction_code,
            'transaction_date'   => $this->transaction_date?->toISOString(),
            'discount'           => $this->discount,
            'total'              => $this->total,
            'paid'               => $this->paid,
            'payment_type'       => $this->payment_type?->value,
            'transaction_status' => $this->transaction_status?->value,
            'customer_id'        => $this->customer_id,
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'name' => $this->createdBy->name,
                ];
            }),
            // 'company_id'         => $this->company_id,
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'ulid'               => $this->ulid,
            'transaction_code'   => $this->transaction_code,
            'transaction_date'   => $this->transaction_date?->toISOString(),
            'discount'           => $this->discount,
            'total'              => $this->total,
            'paid'               => $this->paid,
            'payment_type'       => $this->payment_type?->value,
            'transaction_status' => $this->transaction_status?->value,
            'supplier_id'        => $this->supplier_id,
            'created_by'         => $this->created_by,
            'company_id'         => $this->company_id,
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}

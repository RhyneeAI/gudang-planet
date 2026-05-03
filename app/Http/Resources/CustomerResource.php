<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'             => $this->uuid,
            'name'             => $this->name,
            'address'          => $this->address,
            'phone'            => $this->phone,
            'customer_type_id' => $this->customer_type_id,
            'created_by'       => $this->created_by,
            'company_id'       => $this->company_id,
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
        ];
    }
}
<?php

namespace App\Http\Resources;

use App\Http\Resources\Operational\OpsMandorResource;
use App\Http\Resources\Operational\OpsWalletResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => (string) $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'is_active' => (bool) $this->is_active,
            'mandor' => $this->whenLoaded('mandor', function () {
                $mandor = $this->mandor;

                if (!$mandor->relationLoaded('subCompanies')) {
                    $mandor->setRelation('subCompanies', collect([$this->resource]));
                }

                return new OpsMandorResource($mandor);
            }),
            'created_by' => $this->whenLoaded('createdBy', fn () => [
                'uuid' => $this->createdBy->uuid,
                'name' => $this->createdBy->name,
            ]),
            'wallet' => $this->whenLoaded('wallet', fn () => new OpsWalletResource($this->wallet)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

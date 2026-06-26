<?php

namespace App\Http\Resources;

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $isMandorOrKepala = in_array($this->role, [Role::MANDOR, Role::KEPALA_MANDOR]);
        $subCompanies = $isMandorOrKepala && $this->relationLoaded('subCompanies')
            ? $this->subCompanies
            : collect();

        return [
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role?->value,
            'address'    => $this->address,
            'phone'      => $this->phone,
            'company_id' => $this->company_id,
            'sub_company_uuid' => $this->when(
                $isMandorOrKepala,
                fn () => $subCompanies->count() === 1
                    ? (string) $subCompanies->first()->uuid
                    : null
            ),
            'sub_companies' => $this->when(
                $isMandorOrKepala,
                fn () => $subCompanies->map(fn ($subCompany) => [
                    'uuid' => (string) $subCompany->uuid,
                    'name' => $subCompany->name,
                    'code' => $subCompany->code,
                ])->values()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

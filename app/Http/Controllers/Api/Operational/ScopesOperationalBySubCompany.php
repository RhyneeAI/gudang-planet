<?php

namespace App\Http\Controllers\Api\Operational;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ScopesOperationalBySubCompany
{
    protected function applySubCompanyFilter(Builder $query, Request $request): void
    {
        $user = $request->user();

        if ($user->role === Role::MANDOR) {
            $query->whereHas('subCompany', fn (Builder $subCompanyQuery) => $subCompanyQuery
                ->where('mandor_id', $user->id));
        }

        if ($request->filled('sub_company_uuid')) {
            $query->whereHas(
                'subCompany',
                fn (Builder $subCompanyQuery) => $subCompanyQuery->where('uuid', $request->sub_company_uuid)
            );
        }
    }
}

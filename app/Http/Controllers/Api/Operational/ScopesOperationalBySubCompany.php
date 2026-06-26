<?php

namespace App\Http\Controllers\Api\Operational;

use App\Enums\Role;
use App\Models\SubCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ScopesOperationalBySubCompany
{
    protected function applySubCompanyFilter(Builder $query, Request $request): void
    {
        $user = $request->user();

        if (in_array($user->role, [Role::MANDOR, Role::KEPALA_MANDOR])) {
            $query->where(function (Builder $mandorScope) use ($user) {
                $mandorScope->where('mandor_id', $user->id)
                    ->orWhereHas(
                        'subCompany',
                        fn (Builder $subCompanyQuery) => $subCompanyQuery->where('mandor_id', $user->id)
                    );
            });
        }

        if ($request->filled('sub_company_uuid')) {
            $query->whereHas(
                'subCompany',
                fn (Builder $subCompanyQuery) => $subCompanyQuery->where('uuid', $request->sub_company_uuid)
            );
        }
    }

    protected function mandorCanAccessOperationalRecord(
        User $mandor,
        ?int $recordMandorId,
        ?SubCompany $subCompany,
    ): bool {
        if ($recordMandorId !== null && (int) $recordMandorId === (int) $mandor->id) {
            return true;
        }

        return $subCompany !== null && (int) $subCompany->mandor_id === (int) $mandor->id;
    }
}

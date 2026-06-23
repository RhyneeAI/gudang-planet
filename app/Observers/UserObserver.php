<?php

namespace App\Observers;

use App\Enums\Role;
use App\Models\User;
use App\Services\Absence\AbsEmployeeProfileService;
use App\Services\SubCompanyService;

class UserObserver
{
    public function __construct(
        protected SubCompanyService $subCompanyService,
        protected AbsEmployeeProfileService $employeeProfileService,
    ) {}

    public function created(User $user): void
    {
        if ($user->role === Role::MANDOR && !User::$skipSubCompanyAutoCreate) {
            $this->subCompanyService->createDefaultForMandor($user);
        }

        if ($user->role !== Role::SUPERADMIN) {
            $this->employeeProfileService->syncForUser($user);
        }
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('role')) {
            $this->employeeProfileService->syncForUser($user->fresh());
        }
    }
}

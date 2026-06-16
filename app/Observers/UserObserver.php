<?php

namespace App\Observers;

use App\Enums\Role;
use App\Models\User;
use App\Services\Operational\OpsSubCompanyService;

class UserObserver
{
    public function __construct(
        protected OpsSubCompanyService $subCompanyService,
    ) {}

    public function created(User $user): void
    {
        if ($user->role !== Role::MANDOR) {
            return;
        }

        $this->subCompanyService->createDefaultForMandor($user);
    }
}

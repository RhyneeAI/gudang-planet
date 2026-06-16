<?php

namespace App\Observers;

use App\Enums\Role;
use App\Models\User;
use App\Services\SubCompanyService;

class UserObserver
{
    public function __construct(
        protected SubCompanyService $subCompanyService,
    ) {}

    public function created(User $user): void
    {
        if ($user->role !== Role::MANDOR) {
            return;
        }

        $this->subCompanyService->createDefaultForMandor($user);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use App\Services\Operational\OpsSubCompanyService;
use Illuminate\Database\Seeder;

class OpsSubCompanySeeder extends Seeder
{
    public function run(): void
    {
        $service = app(OpsSubCompanyService::class);

        User::withoutGlobalScopes()
            ->where('company_id', 1)
            ->where('role', Role::MANDOR)
            ->each(fn (User $mandor) => $service->ensureDefaultForMandor($mandor));
    }
}

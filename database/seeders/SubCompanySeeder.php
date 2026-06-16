<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use App\Services\SubCompanyService;
use Illuminate\Database\Seeder;

class SubCompanySeeder extends Seeder
{
    public function run(): void
    {
        $service = app(SubCompanyService::class);

        User::withoutGlobalScopes()
            ->where('company_id', 1)
            ->where('role', Role::MANDOR)
            ->each(fn (User $mandor) => $service->ensureDefaultForMandor($mandor));
    }
}
